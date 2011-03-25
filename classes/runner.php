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

	protected $php = null;
	protected $path = '';
	protected $class = '';
	protected $score = null;
	protected $adapter = null;
	protected $observers = array();
	protected $testObservers = array();
	protected $reports = array();
	protected $testNumber = null;
	protected $testMethodNumber = null;
	protected $codeCoverage = true;

	private $start = null;
	private $stop = null;

	public function __construct(score $score = null, adapter $adapter = null)
	{
		if ($score === null)
		{
			$score = new score();
		}

		if ($adapter === null)
		{
			$adapter = new adapter();
		}

		$this
			->setSuperglobals(new atoum\superglobals())
			->setAdapter($adapter)
			->setScore($score)
		;

		$runnerClass = new \reflectionClass($this);

		$this->path = $runnerClass->getFilename();
		$this->class = $runnerClass->getName();
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

	public function setScore(score $score)
	{
		$this->score = $score;
		return $this;
	}

	public function getScore()
	{
		return $this->score;
	}

	public function getPhp()
	{
		if ($this->php === null)
		{
			if (isset($this->superglobals->_SERVER['_']) === false)
			{
				throw new exceptions\runtime('Unable to find PHP executable');
			}

			$this->setPhp($this->superglobals->_SERVER['_']);
		}

		return $this->php;
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

	public function setAdapter(adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function setPhp($path)
	{
		$this->php = (string) $path;

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

	public function run(array $runTestClasses = array(), array $runTestMethods = array(), $runInChildProcess = true, $testBaseClass = null)
	{
		$this->score->reset();

		$php = $this->getPhp();

		$this->score->setPhpPath($php);
		$this->score->setPhpVersion(\PHP_MAJOR_VERSION . '.' . \PHP_MINOR_VERSION . '.' . \PHP_RELEASE_VERSION);
		$this->score->setAtoumVersion(atoum\test::getVersion());

		$this->start = $this->adapter->microtime(true);

		$this->callObservers(self::runStart);

		if ($testBaseClass === null)
		{
			$testBaseClass = __NAMESPACE__ . '\test';
		}

		if (sizeof($runTestClasses) <= 0)
		{
			$runTestClasses = array_filter($this->adapter->get_declared_classes(), function($class) use ($testBaseClass) { $class = new \reflectionClass($class); return ($class->isSubClassOf($testBaseClass) === true && $class->getParentClass() !== false && $class->isAbstract() === false); });
		}

		$this->testNumber = sizeof($runTestClasses);

		if ($this->testNumber > 0)
		{
			foreach ($runTestClasses as $runTestClass)
			{
				$test = new $runTestClass();

				$xdebugLoaded = $this->codeCoverageIsEnabled() === true && $this->adapter->extension_loaded('xdebug');

				if ($xdebugLoaded === true)
				{
					$this->adapter->xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
				}

				$this->testMethodNumber += sizeof($test);

				if ($test->isIgnored() === false)
				{
					$test->setPhp($php);

					foreach ($this->testObservers as $observer)
					{
						$test->addObserver($observer);
					}

					$this->score->merge($test->run(isset($runTestMethods[$runTestClass]) === false ? array() : $runTestMethods[$runTestClass], $runInChildProcess === false ? null : $this)->getScore());
				}

				if ($xdebugLoaded === true)
				{
					$this->score->getCoverage()->addXdebugData($test, $this->adapter->xdebug_get_code_coverage());

					$this->adapter->xdebug_stop_code_coverage();
				}

				unset($test);
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
