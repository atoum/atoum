<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

abstract class asserter
{
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

	public function getScore()
	{
		return $this->generator->getScore();
	}

	public function getLocale()
	{
		return $this->generator->getLocale();
	}

	public function setGenerator(asserter\generator $generator = null)
	{
		$this->generator = $generator ?: new asserter\generator();

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
				return sprintf($this->getLocale()->_('boolean(%s)'), ($mixed == false ? $this->getLocale()->_('false') : $this->getLocale()->_('true')));

			case 'integer':
				return sprintf($this->getLocale()->_('integer(%s)'), $mixed);

			case 'double':
				return sprintf($this->getLocale()->_('float(%s)'), $mixed);

			case 'NULL':
				return 'null';

			case 'object':
				return sprintf($this->getLocale()->_('object(%s)'), get_class($mixed));

			case 'resource':
				return sprintf($this->getLocale()->_('resource(%s)'), $mixed);

			case 'string':
				return sprintf($this->getLocale()->_('string(%s) \'%s\''), strlen($mixed), $mixed);

			case 'array':
				return sprintf($this->getLocale()->_('array(%s)'), sizeof($mixed));
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

	public abstract function setWith($mixed);

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
