<?php

namespace mageekguy\atoum;

use mageekguy\atoum;
use mageekguy\atoum\asserter;

abstract class asserter
{
	protected $score = null;
	protected $locale = null;
	protected $generator = null;

	public function __construct(score $score, locale $locale, asserter\generator $generator = null)
	{
		$this->score = $score;
		$this->locale = $locale;

		if ($generator === null)
		{
			$generator = new asserter\generator($this->score, $this->locale);
		}

		$this->generator = $generator;
	}

	public function __call($asserter, $arguments)
	{
		return $this->generator->__call($asserter, $arguments);
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

	public abstract function setWith($variable);

	protected function pass()
	{
		$this->score->addPass();
		return $this;
	}

	protected function fail($reason)
	{
		$debugBacktraces = array_slice(debug_backtrace(), 1);

		$asserter = get_class($this);

		foreach ($debugBacktraces as $debugBacktrace)
		{
			if (isset($debugBacktrace['object']) === true && get_class($debugBacktrace['object']) === $asserter && isset($debugBacktrace['file']) === true && isset($debugBacktrace['line']) === true)
			{
				$file = $debugBacktrace['file'];
				$line = $debugBacktrace['line'];
			}

			if (isset($debugBacktrace['class']) === true && is_subclass_of($debugBacktrace['class'], '\mageekguy\atoum\test') === true)
			{
				$class = $debugBacktrace['class'];
				$method = $debugBacktrace['function'];
			}
		}

		throw new asserter\exception($reason, $this->score->addFail($file, $line, $class, $method, $asserter . '::' . $debugBacktraces[0]['function'] . '()', $reason));
	}
}

namespace mageekguy\atoum\asserter;

use mageekguy\atoum;

class exception extends \runtimeException {}

?>
