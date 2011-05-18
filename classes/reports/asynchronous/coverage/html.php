<?php

namespace mageekguy\atoum\reports\asynchronous\coverage;

use
	\mageekguy\atoum,
	\mageekguy\atoum\reports,
	\mageekguy\atoum\template,
	\mageekguy\atoum\exceptions
;

class html extends reports\asynchronous
{
	protected $projectName = '';
	protected $templatesDirectory = null;
	protected $destinationDirectory = null;
	protected $templateParser = null;
	protected $directoryIteratorInjector = null;

	public function __construct($projectName, $templatesDirectory, $destinationDirectory, template\parser $parser = null, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($locale, $adapter);

		if ($parser === null)
		{
			$parser = new template\parser();
		}

		$this
			->setProjectName($projectName)
			->setTemplatesDirectory($templatesDirectory)
			->setDestinationDirectory($destinationDirectory)
			->setTemplateParser($parser)
		;
	}

	public function setTemplatesDirectory($path)
	{
		$this->templatesDirectory = (string) $path;

		return $this;
	}

	public function getTemplatesDirectory()
	{
		return $this->templatesDirectory;
	}

	public function setDestinationDirectory($path)
	{
		$this->destinationDirectory = (string) $path;

		return $this;
	}

	public function getDestinationDirectory()
	{
		return $this->destinationDirectory;
	}

	public function setTemplateParser(template\parser $parser)
	{
		$this->templateParser = $parser;

		return $this;
	}

	public function getTemplateParser()
	{
		return $this->templateParser;
	}

	public function setProjectName($projectName)
	{
		$this->projectName = (string) $projectName;

		return $this;
	}

	public function getProjectName()
	{
		return $this->projectName;
	}

	public function runnerStop(atoum\runner $runner)
	{
		$coverage = $runner->getScore()->getCoverage();

		if (sizeof($coverage) > 0)
		{
			$this->cleanDestinationDirectory();

			$classes = $coverage->getClasses();
			$methods = $coverage->getMethods();

			$index = $this->templateParser->parseFile($this->templatesDirectory . '/index.tpl');

			$codeCoverage = $index->getById('codeCoverage');

			if ($codeCoverage !== null)
			{
				$index->projectName = $this->projectName;

				ksort($classes);

				foreach ($classes as $className => $classFile)
				{
					$codeCoverage->className = $className;
					$codeCoverage->classDate = date('Y-m-d H:i:s', $this->adapter->filemtime($classFile));
					$codeCoverage->classUrl = $classFile;
					$codeCoverage->build();
				}
			}

			$this->adapter->file_put_contents($this->destinationDirectory . '/index.html', (string) $index->build());
		}

		return $this;
	}

	public function setDirectoryIteratorInjector(\closure $directoryIteratorInjector)
	{
		$this->directoryIteratorInjector = $directoryIteratorInjector;

		return $this;
	}

	public function getDirectoryIterator($directory)
	{
		if ($this->directoryIteratorInjector === null)
		{
			$this->setDirectoryIteratorInjector(function($directory) { return new \directoryIterator($directory); });
		}

		return $this->directoryIteratorInjector->__invoke($directory);
	}

	public function cleanDestinationDirectory()
	{
		return $this->cleanDirectory($this->destinationDirectory);
	}

	protected function cleanDirectory($path)
	{
		foreach ($this->getDirectoryIterator($path) as $inode)
		{
			if ($inode->isDot() === false)
			{
				$inodePath = $inode->getPathname();

				if ($inode->isDir() === false)
				{
					$this->adapter->unlink($inodePath);
				}
				else
				{
					$this
						->cleanDirectory($inodePath)
						->adapter->rmdir($inodePath)
					;
				}
			}
		}

		return $this;
	}
}

?>
