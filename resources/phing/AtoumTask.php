<?php

use
	mageekguy\atoum,
	mageekguy\atoum\reports,
	mageekguy\atoum\reports\realtime,
	mageekguy\atoum\report\fields\runner\coverage
;

if (defined('mageekguy\atoum\phing\task\path') === false)
{
	define('mageekguy\atoum\phing\task\path', 'phing/Task.php');
}

require_once mageekguy\atoum\phing\task\path;

class atoumTask extends task
{
	private $runner = false;
	private $fileSets = array();
	private $bootstrap = null;
	private $codeCoverage = false;
	private $codeCoverageReportPath = null;
	private $codeCoverageReportUrl = null;
	private $codeCoverageTreemapPath = null;
	private $codeCoverageTreemapUrl = null;
	private $codeCoverageXunitPath = null;
	private $codeCoverageCloverPath = null;
	private $codeCoverageReportExtensionPath = null;
	private $codeCoverageReportExtensionUrl = null;
	private $branchAndPathCoverage = false;
	private $telemetry = false;
	private $telemetryProjectName = null;
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
		$this->runner = $runner;

		return $this;
	}

	public function getRunner()
	{
		if ($this->runner === null)
		{
			$this->runner = new atoum\runner();
		}

		return $this->runner;
	}

	public function codeCoverageEnabled()
	{
		return ($this->codeCoverage === true || $this->codeCoverageReportPath !== null || $this->codeCoverageTreemapPath !== null);
	}

	public function branchAndPathCoverageEnabled()
	{
		return ($this->branchAndPathCoverage === true);
	}

	public function telemetryEnabled()
	{
		return ($this->telemetry === true || $this->telemetryProjectName !== null);
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
		if (($this->codeCoverage || $this->branchAndPathCoverage) && extension_loaded('xdebug') === false)
		{
			throw new exception('AtoumTask depends on Xdebug being installed to gather code coverage information');
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
		$report = $this->configureDefaultReport();

		$runner = $this->getRunner();

		$runner->addReport($report);

		if ($this->phpPath !== null)
		{
			$this->runner->setPhpPath($this->phpPath);
		}

		if ($this->bootstrap !== null)
		{
			$this->runner->setBootstrapFile($this->bootstrap);
		}

		if ($this->maxChildren > 0)
		{
			$this->runner->setMaxChildrenNumber($this->maxChildren);
		}

		if ($this->codeCoverageEnabled() === false)
		{
			$this->runner->disableCodeCoverage();
		}
		else
		{
			$this->runner->enableCodeCoverage();

			if ($this->branchAndPathCoverageEnabled() === true) {
				$this->runner->enableBranchesAndPathsCoverage();
			}

			if (($path = $this->codeCoverageCloverPath) !== null)
			{
				$clover = new atoum\reports\asynchronous\clover();
				$this->runner->addReport($this->configureAsynchronousReport($clover, $path));
			}

			$coverageReportUrl = null;
			if (($path = $this->codeCoverageReportPath) !== null)
			{
				$coverageHtmlField = new coverage\html(isset($this->project) === true ? $this->project->getName() : 'Code coverage report', $path);
				$coverageHtmlField->setRootUrl($this->codeCoverageReportUrl ?: 'file://' . $path . '/index.html');

				$report->addField($coverageHtmlField);
			}

			if (($path = $this->codeCoverageReportExtensionPath) !== null)
			{
				$coverage = new reports\coverage\html();
				$coverage->addWriter(new atoum\writers\std\out());
				$coverage->setOutPutDirectory($path);
				$this->runner->addReport($coverage);
			}

			if (($path = $this->codeCoverageTreemapPath) !== null)
			{
				$report->addField($this->configureCoverageTreemapField($path, $coverageReportUrl));
			}
		}

		if ($this->telemetryEnabled())
		{
			if (class_exists('mageekguy\atoum\reports\telemetry') === false) {
				throw new exception('AtoumTask depends on atoum/reports-extension being installed to enable telemetry report');
			}

			$telemetry = new reports\telemetry();
			$telemetry->addWriter(new atoum\writers\std\out());

			if ($this->getTelemetryProjectName() !== null) {
				$telemetry->setProjectName($this->getTelemetryProjectName());
			}
			$runner->addReport($telemetry);
		}

		if (($path = $this->codeCoverageXunitPath) !== null)
		{
			$xUnit = new atoum\reports\asynchronous\xunit();
			$this->runner->addReport($this->configureAsynchronousReport($xUnit, $path));
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

		if ($this->showProgress === true)
		{
			$report->showProgress();
		}
		else
		{
			$report->hideProgress();
		}

		if ($this->showDuration === true)
		{
			$report->showDuration();
		}
		else
		{
			$report->hideDuration();
		}

		if ($this->showMemory === true)
		{
			$report->showMemory();
		}
		else
		{
			$report->hideMemory();
		}

		if ($this->showCodeCoverage === true)
		{
			$report->showCodeCoverage();
		}
		else
		{
			$report->hideCodeCoverage();
		}

		if ($this->showMissingCodeCoverage === true)
		{
			$report->showMissingCodeCoverage();
		}
		else
		{
			$report->hideMissingCodeCoverage();
		}

		return $report;
	}

	public function configureAsynchronousReport(reports\asynchronous $report, $path)
	{
		$report->addWriter(new atoum\writers\file($path));

		return $report;
	}

	public function configureCoverageTreemapField($coverageTreemapPath, $coverageReportUrl = null)
	{
		$coverageTreemapField = new coverage\treemap(isset($this->project) ? $this->project->getName() : 'Code coverage treemap', $coverageTreemapPath);
		$coverageTreemapField->setTreemapUrl($this->codeCoverageTreemapUrl ?: 'file://' . $coverageTreemapPath . '/index.html');

		if ($coverageReportUrl !== null)
		{
			$coverageTreemapField->setHtmlReportBaseUrl($coverageReportUrl);
		}

		return $coverageTreemapField;
	}

	public function setBootstrap($bootstrap)
	{
		$this->bootstrap = (string) $bootstrap;

		return $this;
	}

	public function getBootstrap()
	{
		return $this->bootstrap;
	}

	public function setCodeCoverage($codeCoverage)
	{
		$this->codeCoverage = (boolean) $codeCoverage;

		return $this;
	}

	public function getCodeCoverage()
	{
		return $this->codeCoverage;
	}

	public function setBranchAndPathCoverage($branchAndPathCoverage)
	{
		$this->branchAndPathCoverage = (boolean) $branchAndPathCoverage;

		return $this;
	}

	public function getBranchAndPathCoverage()
	{
		return $this->branchAndPathCoverage;
	}

	public function setTelemetry($telemetry)
	{
		$this->telemetry = (boolean) $telemetry;

		return $this;
	}

	public function getTelemetry()
	{
		return $this->telemetry;
	}

	public function setAtoumPharPath($atoumPharPath)
	{
		$this->atoumPharPath = (string) $atoumPharPath;

		return $this;
	}

	public function setTelemetryProjectName($telemetryProjectName)
	{
		$this->telemetryProjectName = (string) $telemetryProjectName;

		return $this;
	}

	public function getTelemetryProjectName()
	{
		return $this->telemetryProjectName;
	}

	public function getAtoumPharPath()
	{
		return $this->atoumPharPath;
	}

	public function setPhpPath($phpPath)
	{
		$this->phpPath = (string) $phpPath;

		return $this;
	}

	public function getPhpPath()
	{
		return $this->phpPath;
	}

	public function setShowCodeCoverage($showCodeCoverage)
	{
		$this->showCodeCoverage = (boolean) $showCodeCoverage;

		return $this;
	}

	public function getShowCodeCoverage()
	{
		return $this->showCodeCoverage;
	}

	public function setShowDuration($showDurationReport)
	{
		$this->showDuration = (boolean) $showDurationReport;

		return $this;
	}

	public function getShowDuration()
	{
		return $this->showDuration;
	}

	public function setShowMemory($showMemoryReport)
	{
		$this->showMemory = (boolean) $showMemoryReport;

		return $this;
	}

	public function getShowMemory()
	{
		return $this->showMemory;
	}

	public function setShowMissingCodeCoverage($showMissingCodeCoverage)
	{
		$this->showMissingCodeCoverage = (boolean) $showMissingCodeCoverage;

		return $this;
	}

	public function getShowMissingCodeCoverage()
	{
		return $this->showMissingCodeCoverage;
	}

	public function setShowProgress($showProgress)
	{
		$this->showProgress = (boolean) $showProgress;

		return $this;
	}

	public function getShowProgress()
	{
		return $this->showProgress;
	}

	public function setAtoumAutoloaderPath($atoumAutoloaderPath)
	{
		$this->atoumAutoloaderPath = (string) $atoumAutoloaderPath;

		return $this;
	}

	public function getAtoumAutoloaderPath()
	{
		return $this->atoumAutoloaderPath;
	}

	public function setCodeCoverageReportPath($codeCoverageReportPath)
	{
		$this->codeCoverageReportPath = (string) $codeCoverageReportPath;

		return $this;
	}

	public function getCodeCoverageReportPath()
	{
		return $this->codeCoverageReportPath;
	}

	public function setCodeCoverageTreemapPath($codeCoverageTreemapPath)
	{
		$this->codeCoverageTreemapPath = (string) $codeCoverageTreemapPath;

		return $this;
	}

	public function setCodeCoverageTreemapUrl($codeCoverageTreemapUrl)
	{
		$this->codeCoverageTreemapUrl = (string) $codeCoverageTreemapUrl;

		return $this;
	}

	public function setCodeCoverageReportUrl($codeCoverageReportUrl)
	{
		$this->codeCoverageReportUrl = (string) $codeCoverageReportUrl;

		return $this;
	}

	public function getCodeCoverageReportUrl()
	{
		return $this->codeCoverageReportUrl;
	}

	public function setCodeCoverageReportExtensionPath($codeCoverageReportExtensionPath)
	{
		$this->codeCoverageReportExtensionPath = (string) $codeCoverageReportExtensionPath;

		return $this;
	}

	public function getCodeCoverageReportExtensionPath()
	{
		return $this->codeCoverageReportExtensionPath;
	}

	public function setCodeCoverageReportExtensionUrl($codeCoverageReportExtensionUrl)
	{
		$this->codeCoverageReportExtensionUrl = (string) $codeCoverageReportExtensionUrl;

		return $this;
	}

	public function getCodeCoverageReportExtensionUrl()
	{
		return $this->codeCoverageReportExtensionUrl;
	}

	public function setMaxChildren($maxChildren)
	{
		$this->maxChildren = (int) $maxChildren;

		return $this;
	}

	public function getMaxChildren()
	{
		return $this->maxChildren;
	}

	public function setCodeCoverageXunitPath($codeCoverageXunitPath)
	{
		$this->codeCoverageXunitPath = (string) $codeCoverageXunitPath;

		return $this;
	}

	public function getCodeCoverageXunitPath()
	{
		return $this->codeCoverageXunitPath;
	}

	public function setCodeCoverageCloverPath($codeCoverageCloverPath)
	{
		$this->codeCoverageCloverPath = (string) $codeCoverageCloverPath;

		return $this;
	}

	public function getCodeCoverageCloverPath()
	{
		return $this->codeCoverageCloverPath;
	}
}
