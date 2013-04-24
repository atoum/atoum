<?php
use
	mageekguy\atoum,
	mageekguy\atoum\reports\realtime
;

if($path = stream_resolve_include_path('phing/Task.php') !== false) {
	require_once $path;
}

class AtoumTask extends Task
{
	private $runner = false;
	private $fileSets = array();
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
	private $maxChildren = 0;

	public function __construct(atoum\runner $runner = null)
	{
		$this->setRunner($runner);
	}

	public function setRunner(atoum\runner $runner = null)
	{
		$this->runner = $runner ?: new atoum\runner();

		return $this;
	}

	public function getRunner()
	{
		return $this->runner;
	}

	public function codeCoverageEnabled()
	{
		return ($this->codeCoverage === true || $this->codeCoverageReportPath !== null);
	}

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

		atoum\scripts\runner::disableAutorun();

		foreach ($this->getFiles() as $file)
		{
			include_once $file;
		}

		return $this->execute();
	}

	public function execute()
	{
		$this->runner->addReport($this->configureDefaultReport());

		if ($this->phpPath !== null)
		{
			$this->runner->setPhpPath($this->phpPath);
		}

		if ($this->maxChildren > 0)
		{
			$this->runner->setMaxChildrenNumber($this->maxChildren);
		}

		if ($this->codeCoverageEnabled() === true)
		{
			$this->runner->enableCodeCoverage();
		}
		else
		{
			$this->runner->disableCodeCoverage();
		}

		if ($this->codeCoverageXunitPath !== null)
		{
			$xUnit = new atoum\reports\asynchronous\xunit();
			$file = new atoum\writers\file($this->codeCoverageXunitPath);
			$this->runner->addReport($xUnit->addWriter($file));
		}

		$score = $this->runner->run();

		$failures = ($score->getUncompletedMethodNumber() + $score->getFailNumber() + $score->getErrorNumber() + $score->getExceptionNumber() + $score->getRuntimeExceptionNumber());
		if ($failures > 0)
		{
			throw new BuildException("Tests did not pass");
		}

		return $this;
	}

	public function configureDefaultReport(realtime\phing $report = null)
	{
		$report = $report ?: new realtime\phing();
		$report->addWriter(new atoum\writers\std\out());

		if($this->showProgress)
		{
			$report->showProgress();
		}
		else
		{
			$report->hideProgress();
		}

		if($this->showDuration)
		{
			$report->showDuration();
		}
		else
		{
			$report->hideDuration();
		}

		if($this->showMemory)
		{
			$report->showMemory();
		}
		else
		{
			$report->hideMemory();
		}

		if($this->showCodeCoverage)
		{
			$report->showCodeCoverage();
		}
		else
		{
			$report->hideCodeCoverage();
		}

		if($this->showMissingCodeCoverage)
		{
			$report->showMissingCodeCoverage();
		}
		else
		{
			$report->hideMissingCodeCoverage();
		}

		return $report;
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
