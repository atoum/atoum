<?php

namespace mageekguy\tests\unit;

class asserter
{
	protected $score = null;

	public final function __construct(score $score)
	{
		$this->score = $score;
	}

	public function __get($asserter)
	{
		$class = __NAMESPACE__ . '\asserters\\' . $asserter;

		if (class_exists($class, true) === false)
		{
			throw new \logicException('Asserter \'' . $class . '\' does not exist');
		}

		return new $class($this->score);
	}

	public function __call($asserter, $arguments)
	{
		$asserter = $this->{$asserter};

		if (sizeof($arguments) > 0)
		{
			$asserter->setWithArguments($arguments);
		}

		return $asserter;
	}

	protected function pass()
	{
		list($file, $line, $class, $method, $asserter) = $this->getBacktrace();
		$this->score->addPassedAssertion($file, $line, $class, $method, $asserter, $reason);
		return $this;
	}

	protected function fail($reason)
	{
		list($file, $line, $class, $method, $asserter) = $this->getBacktrace();
		$this->score->addFailedAssertion($file, $line, $class, $method, $asserter, $reason);
	}

	protected static function toString($mixed)
	{
		switch (true)
		{
			case is_bool($mixed):
				return 'boolean(' . ($mixed == false ? 'false' : 'true') . ')';

			case is_integer($mixed):
				return 'integer(' . $mixed . ')';

			case is_float($mixed):
				return 'float(' . $mixed . ')';

			case is_null($mixed):
				return 'null';

			case is_object($mixed):
				return 'instance of ' . get_class($mixed);

			case is_resource($mixed):
				return 'resource ' . $mixed;

			case is_string($mixed):
				return 'string(' . $mixed . ')';

			case is_array($mixed):
				return 'array(' . sizeof($mixed) . ')';
		}
	}

	private function getBacktrace()
	{
		$debugKey = 0;

		$debugBacktrace = debug_backtrace();

		while (isset($debugBacktrace[$debugKey]['object']) === false || $debugBacktrace[$debugKey]['object'] !== $this)
		{
			$debugKey++;
		}

		do
		{
			$debugKey++;
		}
		while (isset($debugBacktrace[$debugKey]['object']) === true && $debugBacktrace[$debugKey]['object'] === $this);

		$backtrace = array(
			$debugBacktrace[$debugKey - 1]['file'],
			$debugBacktrace[$debugKey - 1]['line'],
			$debugBacktrace[$debugKey]['class'],
			$debugBacktrace[$debugKey]['function'],
			$debugBacktrace[$debugKey - 1]['class'] . '::' . $debugBacktrace[$debugKey - 1]['function'] . '()'
		);

		return $backtrace;
	}
}

?>
