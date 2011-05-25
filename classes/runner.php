<?php

namespace mageekguy\atoum;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

require_once(__DIR__ . '/autoloader.php');

class runner implements observable, adapter\aggregator
{
	const runStart = 'runnerStart';
	const runStop = 'runnerStop';
	const atoumVersionConstant = 'mageekguy\atoum\version';
	const atoumDirectoryConstant = 'mageekguy\atoum\directory';

	protected $path = '';
	protected $class = '';
	protected $score = null;
	protected $locale = null;
	protected $adapter = null;
	protected $observers = array();
	protected $testObservers = array();
	protected $reports = array();
	protected $testNumber = null;
	protected $testMethodNumber = null;
	protected $codeCoverage = true;
	protected $phpPath = null;
	protected $defaultReportTitle = null;

	private $start = null;
	private $stop = null;

	public function __construct(score $score = null, adapter $adapter = null, superglobals $superglobals = null)
	{
		$this
			->setSuperglobals($superglobals ?: new superglobals())
			->setAdapter($adapter ?: new adapter())
			->setScore($score ?: new score())
			->setLocale(new locale())
		;

		$runnerClass = new \reflectionClass($this);

		$this->path = $runnerClass->getFilename();
		$this->class = $runnerClass->getName();
	}

	public function setLocale(locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setSuperglobals(atoum\superglobals $superglobals)
	{
		$this->superglobals = $superglobals;

		return $this;
	}

	public function getSuperglobals()
	{
		return $this->superglobals;
	}

	public function setDefaultReportTitle($title)
	{
		$this->defaultReportTitle = (string) $title;

		return $this;
	}

	public function getDefaultReportTitle()
	{
		return $this->defaultReportTitle;
	}

	public function setScore(score $score)
	{
		$this->score = $score;

		return $this;
	}

	public function getScore()
	{
		return $this->score;
	}

	public function getPhpPath()
	{
		if ($this->phpPath === null)
		{
			if (isset($this->superglobals->_SERVER['_']) === false)
			{
				throw new exceptions\runtime('Unable to find PHP executable');
			}

			$this->setPhpPath($this->superglobals->_SERVER['_']);
		}

		return $this->phpPath;
	}

	public function setAdapter(adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function getTestNumber()
	{
		return $this->testNumber;
	}

	public function getTestMethodNumber()
	{
		return $this->testMethodNumber;
	}

	public function getObservers()
	{
		return $this->observers;
	}

	public function setPhpPath($path)
	{
		$this->phpPath = (string) $path;

		return $this;
	}

	public function enableCodeCoverage()
	{
		$this->codeCoverage = true;

		return $this;
	}

	public function disableCodeCoverage()
	{
		$this->codeCoverage = false;

		return $this;
	}

	public function codeCoverageIsEnabled()
	{
		return $this->codeCoverage;
	}

	public function addObserver(atoum\observers\runner $observer)
	{
		$this->observers[] = $observer;

		return $this;
	}

	public function removeObserver(atoum\observers\runner $observer)
	{
		$this->observers = self::removeFromArray($this->observers, $observer);

		return $this;
	}

	public function callObservers($method)
	{
		foreach ($this->observers as $observer)
		{
			$observer->{$method}($this);
		}

		return $this;
	}

	public function addTestObserver(atoum\observers\test $observer)
	{
		$this->testObservers[] = $observer;

		return $this;
	}

	public function removeTestObserver(atoum\observers\test $observer)
	{
		$this->testObservers = self::removeFromArray($this->testObservers, $observer);

		return $this;
	}

	public function getTestObservers()
	{
		return $this->testObservers;
	}

	public function setPathAndVersionInScore()
	{
		$this->score
			->setAtoumVersion($this->adapter->defined(static::atoumVersionConstant) === false ? null : $this->adapter->constant(static::atoumVersionConstant))
			->setAtoumPath($this->adapter->defined(static::atoumDirectoryConstant) === false ? null : $this->adapter->constant(static::atoumDirectoryConstant))
		;

		$phpPath = $this->adapter->realpath($this->getPhpPath());

		$descriptors = array(
			1 => array('pipe', 'w'),
		);

		$php = @$this->adapter->invoke('proc_open', array($phpPath . ' --version', $descriptors, & $pipes));

		if ($php === false)
		{
			throw new exceptions\runtime('Unable to open \'' . $phpPath . '\'');
		}

		$phpStatus = $this->adapter->proc_get_status($php);

		if ($phpStatus['running'] === false)
		{
			$this->adapter->proc_close($php);

			switch ($phpStatus['exitcode'])
			{
				case 126:
				case 127:
					throw new exceptions\runtime('Unable to find \'' . $phpPath . '\' or it is not executable');
			}
		}

		$phpVersion = $this->adapter->stream_get_contents($pipes[1]);
		$this->adapter->fclose($pipes[1]);

		$this->adapter->proc_close($php);

		$this->score
			->setPhpPath($phpPath)
			->setPhpVersion($phpVersion)
		;

		return $this;
	}

	public function run(array $runTestClasses = array(), array $runTestMethods = array(), $runInChildProcess = true, $testBaseClass = null)
	{
		$this->score->reset();

		if ($runInChildProcess === true)
		{
			$this->setPathAndVersionInScore();
		}

		$this->start = $this->adapter->microtime(true);

		if ($this->defaultReportTitle !== null)
		{
			foreach ($this->reports as $report)
			{
				if ($report->getTitle() === null)
				{
					$report->setTitle($this->defaultReportTitle);
				}
			}
		}

		$this->callObservers(self::runStart);

		if ($testBaseClass === null)
		{
			$testBaseClass = __NAMESPACE__ . '\test';
		}

		if (sizeof($runTestClasses) <= 0)
		{
			$runTestClasses = array_filter($this->adapter->get_declared_classes(), function($class) use ($testBaseClass) {
					$class = new \reflectionClass($class);
					return ($class->isSubClassOf($testBaseClass) === true && $class->isAbstract() === false);
				}
			);
		}

		if (sizeof($runTestClasses) > 0)
		{
			$phpPath = $this->getPhpPath();

			foreach ($runTestClasses as $runTestClass)
			{
				$test = new $runTestClass();

				if ($test->isIgnored() === false)
				{
					$this->testNumber++;
					$this->testMethodNumber += sizeof($test);

					$test
						->setLocale($this->locale)
						->setPhpPath($phpPath)
					;

					foreach ($this->testObservers as $observer)
					{
						$test->addObserver($observer);
					}

					$xdebugLoaded = $this->codeCoverageIsEnabled() === true && $this->adapter->extension_loaded('xdebug');

					if ($xdebugLoaded === true)
					{
						$this->adapter->xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
					}

					$test->run(isset($runTestMethods[$runTestClass]) === false ? array() : $runTestMethods[$runTestClass], $runInChildProcess === false ? null : $this);

					if ($xdebugLoaded === true)
					{
						$this->score->getCoverage()->addXdebugDataForTest($test, $this->adapter->xdebug_get_code_coverage());

						$this->adapter->xdebug_stop_code_coverage();
					}

					$this->score->merge($test->getScore());
				}
			}
		}

		$this->stop = $this->adapter->microtime(true);

		$this->callObservers(self::runStop);

		return $this->score;
	}

	public function getRunningDuration()
	{
		return ($this->start === null || $this->stop === null ? null : $this->stop - $this->start);
	}

	public function addReport(atoum\report $report)
	{
		$this->reports[] = $report;

		return $this
			->addObserver($report)
			->addTestObserver($report)
		;
	}

	public function removeReport(atoum\report $report)
	{
		$this->reports = self::removeFromArray($this->reports, $report);

		return $this
			->removeObserver($report)
			->removeTestObserver($report)
		;
	}

	public function removeReports()
	{
		foreach ($this->reports as $report)
		{
			$this->removeReport($report);
		}

		return $this;
	}

	public function hasReports()
	{
		return (sizeof($this->reports) > 0);
	}

	public function getReports()
	{
		return $this->reports;
	}

	public static function getObserverEvents()
	{
		return array(self::runStart, self::runStop);
	}

	protected static function removeFromArray(array $haystack, $needle)
	{
		$key = array_search($needle, $haystack, true);

		if ($key !== false)
		{
			unset($haystack[$key]);
			$haystack = array_values($haystack);
		}

		return $haystack;
	}
}

?>
