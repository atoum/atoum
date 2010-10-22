<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;

require_once(__DIR__ . '/autoloader.php');

class runner extends atoum\singleton implements observable
{
	const runStart = 'runnerStart';
	const runStop = 'runnerStop';

	protected $runnerObservers = array();
	protected $testObservers = array();

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
		if ($testClass === null)
		{
			$testClass = __NAMESPACE__ . '\test';
		}

		$this->callObservers(self::runStart);

		foreach (array_filter(get_declared_classes(), function($class) use ($testClass) { return (is_subclass_of($class, $testClass) === true && get_parent_class($class) !== false); }) as $class)
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

		$this->callObservers(self::runStop);
	}
}

?>
