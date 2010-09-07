<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;

require(__DIR__ . '/autoloader.php');

class runner implements observable
{
	const testClass = '\mageekguy\atoum\test';

	const eventRunStart = 1;
	const eventRunStop = 2;

	protected $observers = array();

	public function addObserver(observer $observer)
	{
		$this->observers[] = $observer;
		return $this;
	}

	public function getObservers()
	{
		return $this->observers;
	}

	public function sendEventToObservers($event)
	{
		foreach ($this->observers as $observer)
		{
			$observer->manageObservableEvent($this, $event);
		}

		return $this;
	}

	public function run()
	{
		$locale = new atoum\locale();

		$reporter = new atoum\reporters\cli();

		$this->addObserver($reporter);

		$this->sendEventToObservers(self::eventRunStart);

		foreach (get_declared_classes() as $class)
		{
			if (self::isTestClass($class) === true)
			{
				$test = new $class(null, $locale);
				$test->addObserver($reporter);
				$test->run();
			}
		}

		$this->sendEventToObservers(self::eventRunStop);
	}

	protected static function isTestClass($class)
	{
		return (is_subclass_of($class, self::testClass) === true && get_parent_class($class) !== false);
	}
}

?>
