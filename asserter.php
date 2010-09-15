<?php

namespace mageekguy\atoum;

use mageekguy\atoum;
use mageekguy\atoum\asserter;

class asserter
{
	protected $score = null;
	protected $locale = null;

	protected static $aliases = array();

	public function __construct(score $score, locale $locale)
	{
		$this->score = $score;
		$this->locale = $locale;
	}

	public function __call($asserter, $arguments)
	{
		$class = self::getAsserterClass($asserter);

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

	public function toString($mixed)
	{
		switch (true)
		{
			case is_bool($mixed):
				return sprintf($this->locale->_('boolean(%s)'), ($mixed == false ? $this->locale->_('false') : $this->locale->_('true')));

			case is_integer($mixed):
				return sprintf($this->locale->_('integer(%s)'), $mixed);

			case is_float($mixed):
				return sprintf($this->locale->_('float(%s)'), $mixed);

			case is_null($mixed):
				return 'null';

			case is_object($mixed):
				return sprintf($this->locale->_('object(%s)'), get_class($mixed));

			case is_resource($mixed):
				return sprintf($this->locale->_('resource(%s)'), $mixed);

			case is_string($mixed):
				return sprintf($this->locale->_('string(%s) \'%s\''), strlen($mixed), $mixed);

			case is_array($mixed):
				return sprintf($this->locale->_('array(%s)'), sizeof($mixed));
		}
	}

	public static function setAlias($alias, $asserter)
	{
		self::$aliases[$alias] = $asserter;
	}

	public static function resetAliases()
	{
		self::$aliases = array();
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

		throw new asserter\exception($reason, $this->score->addFail($file, $line, $class, $method, $asserter, $reason));
	}

	protected function setWithArguments(array $arguments)
	{
		return $this;
	}

	protected static function getAsserterClass($asserter)
	{
		if (isset(self::$aliases[$asserter]) === true)
		{
			$asserter = self::$aliases[$asserter];
		}

		if (substr($asserter, 0, 1) != '\\')
		{
			$asserter = __NAMESPACE__ . '\asserters\\' . $asserter;
		}

		return $asserter;
	}

	protected function getNamespace()
	{
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

		$backtrace = array();

		if (isset($debugBacktrace[$debugKey + 3]) === true && $debugBacktrace[$debugKey + 3]['function'] == '__call')
		{
			$backtrace[] = $debugBacktrace[$debugKey - 1]['file'];
			$backtrace[] = $debugBacktrace[$debugKey - 1]['line'];
			$backtrace[] = $debugBacktrace[$debugKey + 5]['class'];
			$backtrace[] = $debugBacktrace[$debugKey + 5]['function'];
			$backtrace[] = get_class($this) . '::' . $debugBacktrace[$debugKey - 1]['function'] . '()';
		}
		else if ($debugBacktrace[$debugKey]['function'] != '__call')
		{
			$backtrace[] = $debugBacktrace[$debugKey - 1]['file'];
			$backtrace[] = $debugBacktrace[$debugKey - 1]['line'];
			$backtrace[] = $debugBacktrace[$debugKey]['class'];
			$backtrace[] = $debugBacktrace[$debugKey]['function'];
			$backtrace[] = get_class($this) . '::' . $debugBacktrace[$debugKey - 1]['function'] . '()';
		}
		else
		{
			$backtrace[] = $debugBacktrace[$debugKey]['file'];
			$backtrace[] = $debugBacktrace[$debugKey]['line'];
			$backtrace[] = $debugBacktrace[$debugKey + 2]['class'];
			$backtrace[] = $debugBacktrace[$debugKey + 2]['function'];
			$backtrace[] = get_class($this) . '::' . $debugBacktrace[$debugKey]['args'][0] . '()';
		}

		return $backtrace;
	}
}

namespace mageekguy\atoum\asserter;

use mageekguy\atoum;

class exception extends \runtimeException {}

?>
