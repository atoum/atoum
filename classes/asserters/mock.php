<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

class mock extends atoum\asserter
{
	protected $mock;
	protected $callAsserter;

	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'beforeMethodCall':
			case 'afterMethodCall':
				return call_user_func_array(array($this->getCallAsserter(), $method), array($arguments[0], $this->mock));

			case 'withArguments':
			case 'withIdenticalArguments':
			case 'withAnyArguments':
			case 'withoutAnyArgument':
			case 'withAtLeastArguments':
			case 'withAnyMethodCallsBefore':
			case 'withAnyMethodCallsAfter':
			case 'withAnyFunctionCallsBefore':
			case 'withAnyFunctionCallsAfter':
			case 'beforeFunctionCall':
			case 'afterFunctionCall':
				return call_user_func_array(array($this->getCallAsserter(), $method), $arguments);

			default:
				return parent::__call($method, $arguments);
		}
	}

	public function reset()
	{
		if ($this->mock !== null)
		{
			$this->mock->getMockController()->resetCalls();
		}

		return $this;
	}

	public function setWith($mock)
	{
		$this->mock = $mock;

		if ($this->mock instanceof \mageekguy\atoum\mock\aggregator === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a mock'), $this->getTypeOf($this->mock)));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function getMock()
	{
		return $this->mock;
	}

	public function wasCalled($failMessage = null)
	{
		if (sizeof($this->mockIsSet()->mock->getMockController()->getCalls()) > 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not called'), get_class($this->mock)));
		}

		return $this;
	}

	public function wasNotCalled($failMessage = null)
	{
		if (sizeof($this->mockIsSet()->mock->getMockController()->getCalls()) <= 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is called'), get_class($this->mock)));
		}

		return $this;
	}

	public function getCallAsserter()
	{
		if ($this->mockIsSet()->callAsserter === null)
		{
			$this->callAsserter = new call\mock($this);
		}

		return $this->callAsserter;
	}

	public function call($function)
	{
		return $this->getCallAsserter()->setWith(new php\call($function, null, $this->mock));
	}

	protected function mockIsSet()
	{
		if ($this->mock === null)
		{
			throw new exceptions\logic('Mock is undefined');
		}

		return $this;
	}
}
