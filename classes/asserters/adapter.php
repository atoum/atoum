<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test
;

class adapter extends atoum\asserter
{
	protected $adapter;
	protected $callAsserter;

	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'withArguments':
			case 'withIdenticalArguments':
			case 'withAnyArguments':
			case 'withoutAnyArgument':
			case 'beforeMethodCall':
			case 'afterMethodCall':
			case 'withAnyMethodCallsBefore':
			case 'withAnyMethodCallsAfter':
			case 'withAnyFunctionCallsBefore':
			case 'withAnyFunctionCallsAfter':
				return call_user_func_array(array($this->getCallAsserter(), $method), $arguments);

			case 'beforeFunctionCall':
			case 'afterFunctionCall':
				return call_user_func_array(array($this->getCallAsserter(), $method), array($arguments[0], $this->adapter));

			default:
				return parent::__call($method, $arguments);
		}
	}

	public function reset()
	{
		if ($this->adapter !== null)
		{
			$this->adapter->resetCalls();
		}

		return $this;
	}

	public function getCallAsserter()
	{
		if ($this->adapterIsSet()->callAsserter === null)
		{
			$this->callAsserter = new call\adapter($this);
		}

		return $this->callAsserter;
	}

	public function setWith($adapter)
	{
		$this->adapter = $adapter;

		if ($this->adapter instanceof atoum\test\adapter === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a test adapter'), $this->getTypeOf($this->adapter)));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function call($function)
	{
		return $this->getCallAsserter()->setWith(new php\call($function, null, $this->adapter));
	}

	protected function adapterIsSet()
	{
		if ($this->adapter === null)
		{
			throw new exceptions\logic('Adapter is undefined');
		}

		return $this;
	}
}
