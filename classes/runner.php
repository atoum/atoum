<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;

require_once(__DIR__ . '/autoloader.php');

class runner implements observable, adapter\aggregator
{
	const runStart = 'runnerStart';
	const runStop = 'runnerStop';

	protected $path = '';
	protected $class = '';
	protected $score = null;
	protected $adapter = null;
	protected $observers = array();
	protected $testObservers = array();
	protected $reports = array();
	protected $testNumber = null;
	protected $testMethodNumber = null;

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

		$this->score = $score;
		$this->adapter = $adapter;

		$runnerClass = new \reflectionClass($this);

		$this->path = $runnerClass->getFilename();
		$this->class = $runnerClass->getName();
	}

	public function getScore()
	{
		return $this->score;
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

	public function addObserver(atoum\observers\runner $observer)
	{
		$this->observers[] = $observer;

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

	public function getTestObservers()
	{
		return $this->testObservers;
	}

	public function run(array $runTestClasses = array(), array $runTestMethods = array(), $runInChildProcess = true, $testBaseClass = null)
	{
		$this->score->reset();

		$this->start = $this->adapter->microtime(true);

		$this->callObservers(self::runStart);

		if ($testBaseClass === null)
		{
			$testBaseClass = __NAMESPACE__ . '\test';
		}

		if (sizeof($runTestClasses) <= 0)
		{
			$runTestClasses = array_filter($this->adapter->get_declared_classes(), function($class) use ($testBaseClass) { return (is_subclass_of($class, $testBaseClass) === true && get_parent_class($class) !== false); });
		}

		$this->testNumber = sizeof($runTestClasses);

		foreach ($runTestClasses as $runTestClass)
		{
			$test = new $runTestClass();

			$xdebugLoaded = $this->adapter->extension_loaded('xdebug');

			if ($xdebugLoaded === true)
			{
				$this->adapter->xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
			}

			$this->testMethodNumber += sizeof($test);

			if ($test->isIgnored() === false)
			{
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

	public function getReports()
	{
		return $this->reports;
	}

	public static function getObserverEvents()
	{
		return array(self::runStart, self::runStop);
	}
}

?>
