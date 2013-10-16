<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

abstract class asserter
{
	protected $locale = null;
	protected $generator = null;

	public function __construct(asserter\generator $generator = null)
	{
		$this->setGenerator($generator);
	}

	public function __get($asserter)
	{
		return $this->generator->__get($asserter);
	}

	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'foreach':
				if (isset($arguments[0]) === false || (is_array($arguments[0]) === false && $arguments[0] instanceof \traversable === false))
				{
					throw new exceptions\logic\invalidArgument('First argument of ' . get_class($this) . '::' . $method . '() must be an array or a \traversable instance');
				}
				else if (isset($arguments[1]) === false || $arguments[1] instanceof \closure === false)
				{
					throw new exceptions\logic\invalidArgument('Second argument of ' . get_class($this) . '::' . $method . '() must be a closure');
				}

				foreach ($arguments[0] as $key => $value)
				{
					call_user_func_array($arguments[1], array($this, $value, $key));
				}

				return $this;

			default:
				return $this->generator->__call($method, $arguments);
		}
	}

	public function reset()
	{
		return $this;
	}

	public function setLocale(locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setGenerator(asserter\generator $generator = null)
	{
		$this->generator = $generator ?: new asserter\generator();
		$this->locale = $this->generator->getLocale();

		return $this;
	}

	public function getGenerator()
	{
		return $this->generator;
	}

	public function getTypeOf($mixed)
	{
		switch (gettype($mixed))
		{
			case 'boolean':
				return sprintf($this->locale->_('boolean(%s)'), ($mixed == false ? $this->locale->_('false') : $this->locale->_('true')));

			case 'integer':
				return sprintf($this->locale->_('integer(%s)'), $mixed);

			case 'double':
				return sprintf($this->locale->_('float(%s)'), $mixed);

			case 'NULL':
				return $this->locale->_('null');

			case 'object':
				return sprintf($this->locale->_('object(%s)'), get_class($mixed));

			case 'resource':
				return sprintf($this->locale->_('resource(%s)'), $mixed);

			case 'string':
				return sprintf($this->locale->_('string(%s) \'%s\''), strlen($mixed), $mixed);

			case 'array':
				return sprintf($this->locale->_('array(%s)'), sizeof($mixed));
		}
	}

	public function setWithTest(test $test)
	{
		return $this;
	}

	public function setWithArguments(array $arguments)
	{
		if (sizeof($arguments) > 0)
		{
			call_user_func_array(array($this, 'setWith'), $arguments);
		}

		return $this;
	}

	public function setWith($mixed)
	{
		return $this->reset();
	}

	protected function pass()
	{
		$this->generator->asserterPass($this);

		return $this;
	}

	protected function fail($reason)
	{
		$this->generator->asserterFail($this, $reason);

		return $this;
	}
}
