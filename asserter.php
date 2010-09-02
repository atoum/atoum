<?php

namespace mageekguy\atoum;

use mageekguy\atoum;
use mageekguy\atoum\asserter;

class asserter
{
	protected $score = null;
	protected $locale = null;

	public function __construct(score $score, locale $locale)
	{
		$this->score = $score;
		$this->locale = $locale;
	}

	public function __call($asserter, $arguments)
	{
		$class = __NAMESPACE__ . '\asserters\\' . $asserter;

		if (class_exists($class, true) === false)
		{
			throw new \logicException('Asserter \'' . $class . '\' does not exist');
		}

		$asserter = new $class($this->score, $this->locale);

		if (sizeof($arguments) > 0)
		{
			$asserter->setWithArguments($arguments);
		}

		return $asserter;
	}

	public function getScore()
	{
		return $this->score;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public static function toString($mixed)
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

	protected function pass()
	{
		list($file, $line, $class, $method, $asserter) = $this->getBacktrace();
		$this->score->addPass($file, $line, $class, $method, $asserter);
		return $this;
	}

	protected function fail($reason)
	{
		list($file, $line, $class, $method, $asserter) = $this->getBacktrace();
		$this->score->addFail($file, $line, $class, $method, $asserter, $reason);
		throw new asserter\exception($reason);
	}

	protected function setWithArguments(array $arguments)
	{
		return $this;
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
		);

		if ($debugBacktrace[$debugKey]['function'] != '__call')
		{
			$backtrace[] = $debugBacktrace[$debugKey]['class'];
			$backtrace[] = $debugBacktrace[$debugKey]['function'];
			$backtrace[] = get_class($this) . '::' . $debugBacktrace[$debugKey - 1]['function'] . '()';
		}
		else
		{
			$backtrace[] = $debugBacktrace[$debugKey + 2]['class'];
			$backtrace[] = $debugBacktrace[$debugKey + 2]['function'];
			$backtrace[] = get_class($this) . '::' . $debugBacktrace[$debugKey]['args'][0] . '()';
		}

		return $backtrace;
	}
}

namespace mageekguy\atoum\asserter;

class exception extends \runtimeException {}

?>
