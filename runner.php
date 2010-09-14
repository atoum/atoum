<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;

require(__DIR__ . '/autoloader.php');

class runner implements observable
{
	const testClass = '\mageekguy\atoum\test';

	const runStart = 'runnerStart';
	const runStop = 'runnerStop';

	protected $observers = array();

	public function addObserver(atoum\observers\runner $observer)
	{
		$this->observers[] = $observer;
		return $this;
	}

	public function getObservers()
	{
		return $this->observers;
	}

	public function callObservers($method)
	{
		foreach ($this->observers as $observer)
		{
			$observer->{$method}($this);
		}

		return $this;
	}

	public function run()
	{
		$locale = new atoum\locale();

		$this->callObservers(self::runStart);

		foreach (get_declared_classes() as $class)
		{
			if (self::isTestClass($class) === true)
			{
				$test = new $class(null, $locale);

				foreach ($this->observers as $observer)
				{
					$test->addObserver($observer);
				}

				$test->run();
			}
		}

		$this->callObservers(self::runStop);
	}

	protected static function isTestClass($class)
	{
		return (is_subclass_of($class, self::testClass) === true && get_parent_class($class) !== false);
	}
}

?>
