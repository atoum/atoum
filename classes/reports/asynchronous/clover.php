<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\score
;

class clover extends atoum\reports\asynchronous {
	const defaultTitle = 'atoum code coverage';
	const defaultPackage = 'atoumCodeCoverage';
	const lineTypeMethod = 'method';
	const lineTypeStatement = 'stmt';
	const lineTypeConditional = 'cond';

	private $package;

	protected $score = null;
	protected $loc = 0;
	protected $coveredLoc = 0;
	protected $methods = 0;
	protected $coveredMethods = 0;
	protected $classes = 0;

	public function __construct(atoum\factory $factory = null)
	{
		parent::__construct($factory);

		if ($this->getAdapter()->extension_loaded('libxml') === false)
		{
			throw new exceptions\runtime('libxml PHP extension is mandatory for clover report');
		}
	}

	public function getTitle()
	{
		return $this->title ?: self::defaultTitle;
	}

	public function getPackage()
	{
		return $this->package ?: self::defaultPackage;
	}

	public function setPackage($package)
	{
		$this->package = $package;

		return $this;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		$this->score = ($event !== atoum\runner::runStop) ? null : $observable->getScore();

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
			try
			{
				$package->appendChild($this->makeFileElement($document, $file, $class, $coverage->getCoverageForClass($class)));
			}
			catch (exceptions\logic\invalidArgument $e)
			{
				$package->appendChild($this->makeFileElement($document, $file, $class, array()));
			}
		}

		$package->appendChild($this->makePackageMetricsElement($document, sizeof($coverage->getClasses())));

		return $package;
	}

	protected function makePackageMetricsElement(\DOMDocument $document, $files)
	{
		$metrics = $this->makeFileMetricsElement($document, $this->loc, $this->coveredLoc, $this->methods, $this->coveredMethods, 1);
		$metrics->setAttribute('files', $files);

		return $metrics;
	}

	protected function makeFileElement(\DOMDocument $document, $filename, $class, array $coverage)
	{
		$file = $document->createElement('file');
		$file->setAttribute('name', basename($filename));
		$file->setAttribute('path', $filename);

		$methods = count($coverage);
		$coveredMethods = 0;
		$totalLines = 0;
		$coveredLines = 0;

		foreach ($coverage as $lines)
		{
			if (sizeof($lines) > 0)
			{
				++$coveredMethods;

				foreach ($lines as $lineNumber => $cover)
				{
					if ($cover >= -1)
					{
						$totalLines++;
					}

					if ($cover === 1)
					{
						$coveredLines++;
						$file->appendChild($this->makeLineElement($document, $lineNumber));
					}
				}
			}
		}

		$this
			->addLoc($totalLines)
			->addCoveredLoc($coveredLines)
			->addMethod($methods)
			->addCoveredMethod($coveredMethods)
			->addClasses(1)
		;

		$file->appendChild($this->makeClassElement($document, $class, $coverage));
		$file->appendChild($this->makeFileMetricsElement($document, $totalLines, $coveredLines, $methods, $coveredMethods, $this->classes));

		return $file;
	}

	protected function makeFileMetricsElement(\DOMDocument $document, $loc, $cloc, $methods, $coveredMethods, $classes)
	{
		$metrics = $this->makeClassMetricsElement($document, $loc, $cloc, $methods, $coveredMethods);
		$metrics->setAttribute('classes', $classes);
		$metrics->setAttribute('loc', $loc);
		$metrics->setAttribute('ncloc', $loc);

		return $metrics;
	}

	protected function makeClassElement(\DOMDocument $document, $classname, array $coverage)
	{
		$class = $document->createElement('class');
		$class->setAttribute('name', basename(str_replace('\\', DIRECTORY_SEPARATOR, $classname)));

		$methods = count($coverage);
		$coveredMethods = 0;
		$totalLines = 0;
		$coveredLines = 0;
		foreach ($coverage as $lines)
		{
			if (sizeof($lines) > 0)
			{
				++$coveredMethods;
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
		}

		$class->appendChild($this->makeClassMetricsElement($document, $totalLines, $coveredLines, $methods, $coveredMethods));

		return $class;
	}

	protected function makeClassMetricsElement(\DOMDocument $document, $loc, $cloc, $methods, $cmethods)
	{
		$metrics = $document->createElement('metrics');
		$metrics->setAttribute('complexity', 0);
		$metrics->setAttribute('elements', $loc);
		$metrics->setAttribute('coveredelements', $cloc);
		$metrics->setAttribute('conditionals', 0);
		$metrics->setAttribute('coveredconditionals', 0);
		$metrics->setAttribute('statements', $loc);
		$metrics->setAttribute('coveredstatements', $cloc);
		$metrics->setAttribute('methods', $methods);
		$metrics->setAttribute('coveredmethods', $cmethods);
		$metrics->setAttribute('testduration', 0);
		$metrics->setAttribute('testfailures', 0);
		$metrics->setAttribute('testpasses', 0);
		$metrics->setAttribute('testruns', 0);

		return $metrics;
	}

	protected function makeLineElement(\DOMDocument $document, $linenum)
	{
		$line = $document->createElement('line');
		$line->setAttribute('num', $linenum);
		$line->setAttribute('type', self::lineTypeStatement);
		$line->setAttribute('complexity', 0);
		$line->setAttribute('count', 1);
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

	protected function addClasses($count)
	{
		$this->classes += $count;

		return $this;
	}
}
