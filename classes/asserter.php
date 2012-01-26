<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

/**
 * @property    \mageekguy\atoum\asserter                       if
 * @property    \mageekguy\atoum\asserter                       and
 * @property    \mageekguy\atoum\asserter                       then
 *
 * @method      \mageekguy\atoum\asserter                       if()
 * @method      \mageekguy\atoum\asserter                       and()
 * @method      \mageekguy\atoum\asserter                       then()
 *
 * @method      \mageekguy\atoum\asserters\adapter              adapter()
 * @method      \mageekguy\atoum\asserters\afterDestructionOf   afterDestructionOf()
 * @method      \mageekguy\atoum\asserters\phpArray             array()
 * @method      \mageekguy\atoum\asserters\boolean              boolean()
 * @method      \mageekguy\atoum\asserters\castToString         castToString()
 * @method      \mageekguy\atoum\asserters\phpClass             class()
 * @method      \mageekguy\atoum\asserters\dateTime             dateTime()
 * @method      \mageekguy\atoum\asserters\error                error()
 * @method      \mageekguy\atoum\asserters\exception            exception()
 * @method      \mageekguy\atoum\asserters\float                float()
 * @method      \mageekguy\atoum\asserters\hash                 hash()
 * @method      \mageekguy\atoum\asserters\integer              integer()
 * @method      \mageekguy\atoum\asserters\mock                 mock()
 * @method      \mageekguy\atoum\asserters\mysqlDateTime        mysqlDateTime()
 * @method      \mageekguy\atoum\asserters\object               object()
 * @method      \mageekguy\atoum\asserters\output               output()
 * @method      \mageekguy\atoum\asserters\phpArray             phpArray()
 * @method      \mageekguy\atoum\asserters\phpClass             phpClass()
 * @method      \mageekguy\atoum\asserters\sizeOf               sizeOf()
 * @method      \mageekguy\atoum\asserters\stream               stream()
 * @method      \mageekguy\atoum\asserters\string               string()
 * @method      \mageekguy\atoum\asserters\testedClass          testedClass()
 * @method      \mageekguy\atoum\asserters\variable             variable()
 */
abstract class asserter
{
	protected $generator = null;

	public function __construct(asserter\generator $generator)
	{
		$this->generator = $generator;
	}

	public function __get($asserter)
	{
		return $this->generator->__get($asserter);
	}

	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->generator, $method), $arguments);
	}

	public function reset()
	{
		return $this;
	}

	public function getScore()
	{
		return $this->generator->getScore();
	}

	public function getLocale()
	{
		return $this->generator->getLocale();
	}

	public function getGenerator()
	{
		return $this->generator;
	}

	public function getTypeOf($mixed)
	{
		switch (true)
		{
			case is_bool($mixed):
				return sprintf($this->getLocale()->_('boolean(%s)'), ($mixed == false ? $this->getLocale()->_('false') : $this->getLocale()->_('true')));

			case is_integer($mixed):
				return sprintf($this->getLocale()->_('integer(%s)'), $mixed);

			case is_float($mixed):
				return sprintf($this->getLocale()->_('float(%s)'), $mixed);

			case is_null($mixed):
				return 'null';

			case is_object($mixed):
				return sprintf($this->getLocale()->_('object(%s)'), get_class($mixed));

			case is_resource($mixed):
				return sprintf($this->getLocale()->_('resource(%s)'), $mixed);

			case is_string($mixed):
				return sprintf($this->getLocale()->_('string(%s) \'%s\''), strlen($mixed), $mixed);

			case is_array($mixed):
				return sprintf($this->getLocale()->_('array(%s)'), sizeof($mixed));
		}
	}

	public function must(\closure $closure)
	{
		$closure($this);

		return $this;
	}

	public abstract function setWith($mixed);

	protected function pass()
	{
		$test = $this->generator->getTest();

		if ($test !== null)
		{
			$test->getScore()->addPass();
		}

		return $this;
	}

	protected function fail($reason)
	{
		$failId = null;

		$test = $this->generator->getTest();

		if ($test !== null)
		{
			$line = null;
			$function = null;
			$class = $test->getClass();
			$method = $test->getCurrentMethod();
			$file = $test->getPath();

			foreach (array_filter(debug_backtrace(), function($backtrace) use ($file) { return isset($backtrace['file']) === true && $backtrace['file'] === $file; }) as $backtrace)
			{
				if ($line === null && isset($backtrace['line']) === true)
				{
					$line = $backtrace['line'];
				}

				if ($function === null && isset($backtrace['object']) === true && isset($backtrace['function']) === true && $backtrace['object'] === $this && $backtrace['function'] !== '__call')
				{
					$function = $backtrace['function'];
				}
			}

			$failId = $test->getScore()->addFail($file, $line, $class, $method, get_class($this) . ($function ? '::' . $function : '') . '()', $reason);
		}

		throw new asserter\exception($reason, $failId);
	}
}

?>
