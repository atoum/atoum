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
		$test = atoum\test::$runningTest;

		$class = $test->getClass();
		$method = $test->getCurrentMethod();
		$file = $test->getPath();
		$asserter = get_class($this);
		$line = null;

		$backtraces = array_filter(debug_backtrace(), function($value) use ($file) { return (isset($value['file']) === true && $value['file'] === $file); });

		foreach ($backtraces as $backtrace)
		{
			if ($backtrace['object'] === $this)
			{
				$asserter .= '::' . $backtrace['function'] . '()';
				$line = $backtrace['line'];
			}
		}

		throw new asserter\exception($reason, $this->score->addFail($file, $line, $class, $method, $asserter, $reason));
	}
}

namespace mageekguy\atoum\asserter;

use mageekguy\atoum;

class exception extends \runtimeException {}

?>
