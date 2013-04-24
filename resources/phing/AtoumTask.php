<?php

require_once "phing/Task.php";

class AtoumTask extends Task
{
	private $runner = false;
	private $fileSets = array();
	private $configurationFiles = array();
	private $bootstrap = null;
	private $codeCoverage = false;
	private $codeCoverageReportPath = null;
	private $codeCoverageReportUrl = null;
	private $codeCoverageXunitPath = null;
	private $atoumPharPath = null;
	private $atoumAutoloaderPath = null;
	private $phpPath = null;
	private $showProgress = true;
	private $showDuration = true;
	private $showMemory = true;
	private $showCodeCoverage = true;
	private $showMissingCodeCoverage = true;
	private $maxChildren = false;
	private $message = null;

	public function createFileSet()
	{
		$this->fileSets[] = $fileSet = new Fileset();

		return $fileSet;
	}

	private function getFiles()
	{
		$files = array();

		foreach ($this->fileSets as $fs)
		{
			$ds = $fs->getDirectoryScanner($this->project);
			$dir = $fs->getDir($this->project);
			$srcFiles = $ds->getIncludedFiles();

			foreach ($srcFiles as $file)
			{
				$files[] = $dir . FileSystem::getFileSystem()->getSeparator() . $file;
			}
		}

		return $files;
	}

	public function setMessage($str)
	{
		$this->message = $str;

		return $this;
	}

	public function init() {}

	public function main()
	{
		if ($this->codeCoverage && extension_loaded('xdebug') === false)
		{
			throw new exception('AtoumTask depends on Xdebug being installed to gather code coverage information');
		}

		if ($this->bootstrap)
		{
			require_once $this->bootstrap;
		}

		if ($this->atoumPharPath !== null)
		{
			require_once $this->atoumPharPath;
		}
		else if ($this->atoumAutoloaderPath !== null)
		{
			require_once $this->atoumAutoloaderPath;
		}
		else if (class_exists('mageekguy\atoum\scripts\runner', false) === false)
		{
			throw new exception('Unknown class mageekguy\\atoum\\scripts\\runner, consider setting atoumPharPath parameter');
		}

		mageekguy\atoum\scripts\runner::disableAutorun();

		foreach ($this->getFiles() as $file)
		{
			include_once $file;
		}

		return $this->execute();
	}

	public function execute()
	{
		if ($this->runner === false)
		{
			$this->runner = new \mageekguy\atoum\runner();

			$report = new \mageekguy\atoum\reports\realtime\phing(
				$this->showProgress,
				$this->showCodeCoverage,
				$this->showMissingCodeCoverage,
				$this->showDuration,
				$this->showMemory,
				$this->codeCoverageReportPath,
				$this->codeCoverageReportUrl
			);

			$writer = new \mageekguy\atoum\writers\std\out();

			$report->addWriter($writer);

			$this->runner->addReport($report);

			if ($this->phpPath !== null)
			{
				$this->runner->setPhpPath($this->phpPath);
			}

			if ($this->maxChildren !== false)
			{
				$this->runner->setMaxChildrenNumber($this->maxChildren);
			}

			if ($this->codeCoverage === true)
			{
				$this->runner->enableCodeCoverage();
			}
			else
			{
				$this->runner->disableCodeCoverage();
			}

			if ($this->codeCoverageXunitPath !== false)
			{
				$xUnit = new \mageekguy\atoum\reports\asynchronous\xunit();

				$file = new \mageekguy\atoum\writers\file($this->codeCoverageXunitPath);
				$xUnit->addWriter($file);

				$this->runner->addReport($xUnit);
			}
		}

		$this->runner->run();

		$score = $this->runner->getScore();

		if (sizeof($score->getErrors()) > 0 || sizeof($score->getFailAssertions()) > 0 || sizeof($score->getExceptions()) > 0)
		{
			throw new BuildException("Tests did not pass");
		}

		return $this;
	}

	public function setBootstrap($bootstrap)
	{
		$this->bootstrap = (string) $bootstrap;

		return $this;
	}

	public function setCodeCoverage($codeCoverage)
	{
		$this->codeCoverage = (boolean) $codeCoverage;

		return $this;
	}

	public function setConfigurationFiles(array $configurationFiles)
	{
		$this->configurationFiles = $configurationFiles;

		return $this;
	}

	public function getConfigurationFiles()
	{
		return $this->configurationFiles;
	}

	public function setAtoumPharPath($atoumPharPath)
	{
		$this->atoumPharPath = (string) $atoumPharPath;

		return $this;
	}

	public function setPhpPath($phpPath)
	{
		$this->phpPath = (string) $phpPath;

		return $this;
	}

	public function setShowCodeCoverage($showCodeCoverage)
	{
		$this->showCodeCoverage = (boolean) $showCodeCoverage;

		return $this;
	}

	public function setShowDuration($showDurationReport)
	{
		$this->showDuration = (boolean) $showDurationReport;

		return $this;
	}

	public function setShowMemory($showMemoryReport)
	{
		$this->showMemory = (boolean) $showMemoryReport;

		return $this;
	}

	public function setShowMissingCodeCoverage($showMissingCodeCoverage)
	{
		$this->showMissingCodeCoverage = (boolean) $showMissingCodeCoverage;

		return $this;
	}

	public function setShowProgress($showProgress)
	{
		$this->showProgress = (boolean) $showProgress;

		return $this;
	}

	public function setAtoumAutoloaderPath($atoumAutoloaderPath)
	{
		$this->atoumAutoloaderPath = $atoumAutoloaderPath;

		return $this;
	}

	public function setCodeCoverageReportPath($codeCoverageReportPath)
	{
		$this->codeCoverageReportPath = (string) $codeCoverageReportPath;

		return $this;
	}

	public function setCodeCoverageReportUrl($codeCoverageReportUrl)
	{
		$this->codeCoverageReportUrl = (string) $codeCoverageReportUrl;

		return $this;
	}

	public function setMaxChildren($maxChildren)
	{
		$this->maxChildren = (int) $maxChildren;

		return $this;
	}

	public function setCodeCoverageXunitPath($codeCoverageXunitPath)
	{
		$this->codeCoverageXunitPath = $codeCoverageXunitPath;

		return $this;
	}

	public function getCodeCoverageXunitPath()
	{
		return $this->codeCoverageXunitPath;
	}
}
