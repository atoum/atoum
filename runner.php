<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;

require(__DIR__ . '/autoloader.php');

class runner implements observable
{
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

	public function run(\closure $depedenciesInjecter = null, $testClass = '\mageekguy\atoum\test')
	{
		$locale = new atoum\locale();

		$this->callObservers(self::runStart);

		foreach (array_filter(get_declared_classes(), function($class) use ($testClass) { return (is_subclass_of($class, $testClass) === true && get_parent_class($class) !== false); }) as $class)
		{
			$test = new $class(null, $locale);

			if ($depedenciesInjecter !== null)
			{
				$depedenciesInjecter($test);
			}

			$test->run();
		}

		$this->callObservers(self::runStop);
	}
}

?>
