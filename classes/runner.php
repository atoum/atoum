<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;

require_once(__DIR__ . '/autoloader.php');

class runner implements observable, adapter\aggregator
{
	const runStart = 'runnerStart';
	const runStop = 'runnerStop';

	protected $score = null;
	protected $adapter = null;
	protected $observers = array();
	protected $testObservers = array();
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
	}

	public function getScore()
	{
		return $this->score;
	}

	public function getAdapter()
	{
		return $this->adapter;
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

	public function run($testClass = null)
	{
		$this->score->reset();

		$this->start = $this->adapter->microtime(true);

		if ($testClass === null)
		{
			$testClass = __NAMESPACE__ . '\test';
		}

		$this->callObservers(self::runStart);

		$testClasses = array_filter($this->adapter->get_declared_classes(), function($class) use ($testClass) { return (is_subclass_of($class, $testClass) === true && get_parent_class($class) !== false); });

		$this->testNumber = sizeof($testClasses);

		foreach ($testClasses as $testClass)
		{
			$test = new $testClass();

			$this->testMethodNumber += sizeof($test);

			if ($test->isIgnored() === false)
			{
				foreach ($this->testObservers as $observer)
				{
					$test->addObserver($observer);
				}

				$this->score->merge($test->run()->getScore());
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

	public static function getObserverEvents()
	{
		return array(self::runStart, self::runStop);
	}

}

?>
