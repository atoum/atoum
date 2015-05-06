<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\score
;

class clover extends atoum\reports\asynchronous
{
	const defaultTitle = 'atoum code coverage';
	const defaultPackage = 'atoumCodeCoverage';
	const lineTypeMethod = 'method';
	const lineTypeStatement = 'stmt';
	const lineTypeConditional = 'cond';

	protected $score = null;
	protected $loc = 0;
	protected $coveredLoc = 0;
	protected $methods = 0;
	protected $coveredMethods = 0;
	protected $branches = 0;
	protected $coveredBranches = 0;
	protected $paths = 0;
	protected $classes = 0;
	protected $package = '';

	public function __construct(atoum\adapter $adapter = null)
	{
		parent::__construct();

		$this->setAdapter($adapter);

		if ($this->adapter->extension_loaded('libxml') === false)
		{
			throw new exceptions\runtime('libxml PHP extension is mandatory for clover report');
		}
	}

	public function getTitle()
	{
		return ($this->title ?: self::defaultTitle);
	}

	public function getPackage()
	{
		return ($this->package ?: self::defaultPackage);
	}

	public function setPackage($package)
	{
		$this->package = (string) $package;

		return $this;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		$this->score = ($event !== atoum\runner::runStop ? null : $observable->getScore());

		return parent::handleEvent($event, $observable);
	}

	public function build($event)
	{
		if ($event === atoum\runner::runStop)
		{
			$document = new \DOMDocument('1.0', 'UTF-8');

			$document->formatOutput = true;
			$document->appendChild($this->makeRootElement($document, $this->score->getCoverage()));

			$this->string = $document->saveXML();
		}

		return $this;
	}

	protected function makeRootElement(\DOMDocument $document, score\coverage $coverage)
	{
		$root = $document->createElement('coverage');

		$root->setAttribute('generated', $this->getAdapter()->time());
		$root->setAttribute('clover', $this->getAdapter()->uniqid());

		$root->appendChild($this->makeProjectElement($document, $coverage));

		return $root;
	}

	protected function makeProjectElement(\DOMDocument $document, score\coverage $coverage)
	{
		$project = $document->createElement('project');

		$project->setAttribute('timestamp', $this->getAdapter()->time());
		$project->setAttribute('name', $this->getTitle());

		$project->appendChild($this->makePackageElement($document, $coverage));
		$project->appendChild($this->makeProjectMetricsElement($document, sizeof($coverage->getClasses())));

		return $project;
	}

	protected function makeProjectMetricsElement(\DOMDocument $document, $files)
	{
		$metrics = $this->makePackageMetricsElement($document, $files);

		$metrics->setAttribute('packages', 1);

		return $metrics;
	}

	protected function makePackageElement(\DOMDocument $document, score\coverage $coverage)
	{
		$package = $document->createElement('package');

		$package->setAttribute('name', $this->getPackage());

		foreach ($coverage->getClasses() as $class => $file)
		{
			$package->appendChild($this->makeFileElement($document, $file, $class, $coverage->getCoverageForClass($class), $coverage->getBranchesCoverageForClass($class), $coverage->getPathsCoverageForClass($class)));
		}

		$package->appendChild($this->makePackageMetricsElement($document, sizeof($coverage->getClasses())));

		return $package;
	}

	protected function makePackageMetricsElement(\DOMDocument $document, $files)
	{
		$metrics = $this->makeFileMetricsElement($document, $this->loc, $this->coveredLoc, $this->methods, $this->coveredMethods, $this->classes, $this->branches, $this->coveredBranches, $this->paths);

		$metrics->setAttribute('files', $files);

		return $metrics;
	}

	protected function makeFileElement(\DOMDocument $document, $filename, $class, array $coverage, array $branches, array $paths)
	{
		$file = $document->createElement('file');

		$file->setAttribute('name', basename($filename));
		$file->setAttribute('path', $filename);

		$methods = sizeof($coverage);
		$coveredMethods = 0;
		$totalLines = $coveredLines = 0;
		$totalBranches = $coveredBranches = 0;
		$totalPaths = 0;

		foreach ($coverage as $method => $lines)
		{
			$totalMethodLines = $coveredMethodLines = 0;

			if (isset($branches[$method]))
			{
				$totalBranches += sizeof($branches[$method]);
				$coveredBranches += sizeof(array_filter($branches[$method], function(array $branch) { return $branch['hit'] === 1; }));
			}

			if (isset($paths[$method]))
			{
				$totalPaths += sizeof($paths[$method]);
			}

			foreach ($lines as $lineNumber => $cover)
			{
				if ($cover >= -1)
				{
					$totalMethodLines++;
				}

				if ($cover === 1)
				{
					$coveredMethodLines++;
					$file->appendChild($this->makeLineElement($document, $lineNumber));
				}
				else
				{
					if ($cover !== -2)
					{
						$file->appendChild($this->makeLineElement($document, $lineNumber, 0));
					}
				}
			}

			if ($coveredMethodLines === $totalMethodLines)
			{
				++$coveredMethods;
			}

			$totalLines += $totalMethodLines;
			$coveredLines += $coveredMethodLines;
		}

		$this
			->addLoc($totalLines)
			->addCoveredLoc($coveredLines)
			->addClasses(1)
			->addMethod($methods)
			->addCoveredMethod($coveredMethods)
			->addBranches($totalBranches)
			->addCoveredBranches($coveredBranches)
			->addPaths($totalPaths)
		;

		$file->appendChild($this->makeClassElement($document, $class, $coverage, $branches, $paths));
		$file->appendChild($this->makeFileMetricsElement($document, $totalLines, $coveredLines, $methods, $coveredMethods, 1, $totalBranches, $coveredBranches, $totalPaths));

		return $file;
	}

	protected function makeFileMetricsElement(\DOMDocument $document, $loc, $cloc, $methods, $coveredMethods, $classes, $branches = 0, $coveredBranches = 0, $complexity = 0)
	{
		$metrics = $this->makeClassMetricsElement($document, $loc, $cloc, $methods, $coveredMethods, $branches, $coveredBranches, $complexity);

		$metrics->setAttribute('classes', $classes);
		$metrics->setAttribute('loc', $loc);
		$metrics->setAttribute('ncloc', $loc);

		return $metrics;
	}

	protected function makeClassElement(\DOMDocument $document, $classname, array $coverage, array $branches, array $paths)
	{
		$class = $document->createElement('class');

		$class->setAttribute('name', basename(str_replace('\\', DIRECTORY_SEPARATOR, $classname)));

		$methods = sizeof($coverage);
		$coveredMethods = 0;
		$totalLines = $coveredLines = 0;
		$totalBranches = $coveredBranches = 0;
		$totalPaths = 0;

		foreach ($coverage as $method => $lines)
		{
			if (isset($branches[$method]))
			{
				$totalBranches += sizeof($branches[$method]);
				$coveredBranches += sizeof(array_filter($branches[$method], function(array $branch) { return $branch['hit'] === 1; }));
			}

			if (isset($paths[$method]))
			{
				$totalPaths += sizeof($paths[$method]);
			}

			foreach ($lines as $cover)
			{
				if ($cover >= -1)
				{
					$totalLines++;
				}

				if ($cover === 1)
				{
					$coveredLines++;
				}
			}

			if ($totalLines === $coveredLines)
			{
				++$coveredMethods;
			}
		}

		$class->appendChild($this->makeClassMetricsElement($document, $totalLines, $coveredLines, $methods, $coveredMethods, $totalBranches, $coveredBranches, $totalPaths));

		return $class;
	}

	protected function makeClassMetricsElement(\DOMDocument $document, $loc, $coveredLines, $methods, $coveredMethods, $branches = 0, $coveredBranches = 0, $complexity = 0)
	{
		$metrics = $document->createElement('metrics');

		$metrics->setAttribute('complexity', $complexity);
		$metrics->setAttribute('elements', $loc + $methods + $branches);
		$metrics->setAttribute('coveredelements', $coveredLines + $coveredMethods + $coveredBranches);
		$metrics->setAttribute('conditionals', $branches);
		$metrics->setAttribute('coveredconditionals', $coveredBranches);
		$metrics->setAttribute('statements', $loc);
		$metrics->setAttribute('coveredstatements', $coveredLines);
		$metrics->setAttribute('methods', $methods);
		$metrics->setAttribute('coveredmethods', $coveredMethods);
		$metrics->setAttribute('testduration', 0);
		$metrics->setAttribute('testfailures', 0);
		$metrics->setAttribute('testpasses', 0);
		$metrics->setAttribute('testruns', 0);

		return $metrics;
	}

	protected function makeLineElement(\DOMDocument $document, $linenum, $count = 1)
	{
		$line = $document->createElement('line');

		$line->setAttribute('num', $linenum);
		$line->setAttribute('type', self::lineTypeStatement);
		$line->setAttribute('complexity', 0);
		$line->setAttribute('count', $count);
		$line->setAttribute('falsecount', 0);
		$line->setAttribute('truecount', 0);
		$line->setAttribute('signature', '');
		$line->setAttribute('testduration', 0);
		$line->setAttribute('testsuccess', 0);

		return $line;
	}

	protected function addLoc($count)
	{
		$this->loc += $count;

		return $this;
	}

	protected function addCoveredLoc($count)
	{
		$this->coveredLoc += $count;

		return $this;
	}

	protected function addMethod($count)
	{
		$this->methods += $count;

		return $this;
	}

	protected function addCoveredMethod($count)
	{
		$this->coveredMethods += $count;

		return $this;
	}

	protected function addBranches($count)
	{
		$this->branches += $count;

		return $this;
	}

	protected function addCoveredBranches($count)
	{
		$this->coveredBranches += $count;

		return $this;
	}

	protected function addPaths($count)
	{
		$this->paths += $count;

		return $this;
	}

	protected function addClasses($count)
	{
		$this->classes += $count;

		return $this;
	}
}
