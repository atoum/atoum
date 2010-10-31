<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;

require_once(__DIR__ . '/autoloader.php');

class runner implements observable, adapter\aggregator
{
	const runStart = 'runnerStart';
	const runStop = 'runnerStop';

	protected $adapter = null;
	protected $runnerObservers = array();
	protected $testObservers = array();

	private $start = null;
	private $stop = null;

	public function __construct(adapter $adapter = null)
	{
		if ($adapter === null)
		{
			$adapter = new adapter();
		}

		$this->adapter = $adapter;
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

	public function addObserver(atoum\observer $observer)
	{
		switch (true)
		{
			case $observer instanceof atoum\observers\runner:
				$this->runnerObservers[] = $observer;

			case $observer instanceof atoum\observers\test:
				$this->testObservers[] = $observer;
		}

		return $this;
	}

	public function getObservers()
	{
		return array_merge($this->runnerObservers, $this->testObservers);
	}

	public function hasObservers()
	{
		return (sizeof($this->runnerObservers) > 0 || sizeof($this->testObservers) > 0);
	}

	public function callObservers($method)
	{
		foreach ($this->runnerObservers as $observer)
		{
			$observer->{$method}($this);
		}

		return $this;
	}

	public function run($testClass = null)
	{
		$this->start = $this->adapter->microtime(true);

		if ($testClass === null)
		{
			$testClass = __NAMESPACE__ . '\test';
		}

		$this->callObservers(self::runStart);

		foreach (array_filter($this->adapter->get_declared_classes(), function($class) use ($testClass) { return (is_subclass_of($class, $testClass) === true && get_parent_class($class) !== false); }) as $class)
		{
			$test = new $class();

			if ($test->isIgnored() === false)
			{
				foreach ($this->testObservers as $observer)
				{
					$test->addObserver($observer);
				}

				$test->run();
			}
		}

		$this->stop = $this->adapter->microtime(true);

		$this->callObservers(self::runStop);
	}

	public function getRunningDuration()
	{
		return ($this->start === null || $this->stop === null ? null : $this->stop -  $this->start);
	}
}

?>
