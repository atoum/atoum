<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class mock extends atoum\asserter
{
	protected $mock = null;
	protected $currentMethod = null;
	protected $currentArguments = null;
	protected $currentCalls = null;

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

	public function call($method, array $arguments = null, $failMessage = null)
	{
		$calls = $this->mockIsSet()->mock->getMockController()->getCalls($method);

		if (sizeof($calls) <= 0)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('method %s::%s() is not called'), get_class($this->mock), $method));
		}
		else if ($arguments !== null && in_array($arguments, $calls) === false)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->__('method %s::%s() is not called with this argument', 'method %s::%s() is not called with these arguments', sizeof($arguments)), get_class($this->mock), $method));
		}
		else
		{
			$this->pass();

			$this->currentMethod = $method;
			$this->currentArguments = $arguments;
			$this->currentCalls = array_keys($calls);
		}

		return $this;
	}

	public function notCall($method, array $arguments = null, $failMessage = null)
	{
		$calls = $this->mockIsSet()->mock->getMockController()->getCalls($method);

		if (sizeof($calls) <= 0)
		{
			$this->pass();
		}
		else if ($arguments === null)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('method %s::%s() is called'), get_class($this->mock), $method));
		}
		else if (in_array($arguments, $calls) === true)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->__('method %s::%s() is called with this argument', 'method %s::%s() is called with these arguments', sizeof($arguments)), get_class($this->mock), $method));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function after($method, array $arguments = null, $adapter = null, $failMessage = null)
	{
		if ($adapter === null)
		{
			$adapter = $this->mockIsSet()->mock->getMockController();
		}
		else if ($adapter instanceof atoum\mock\aggregator)
		{
			$adapter = $adapter->getMockController();
		}

		$calls = $adapter->getCalls($method);


		if (sizeof($calls) <= 0)
		{
			if ($adapter instanceof atoum\mock\controller)
			{
				$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('method %s::%s() is not called'), get_class($adapter), $method));
			}
			else
			{
				$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('function %s() is not called'), $method));
			}
		}

		if ($arguments !== null)
		{
			$calls = array_filter($calls, function($value) use ($arguments) { return $value === $arguments; });
		}

		$calls = array_keys($calls);

		if (current($calls) > current($this->currentCalls))
		{
			if ($adapter instanceof atoum\mock\controller)
			{
				$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('method %s::%s() is not called after method %s::%s()'), get_class($this->mock), $this->currentMethod, get_class($adapter), $method));
			}
			else
			{
				$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('method %s::%s() is not called after function %s()'), get_class($this->mock), $this->currentMethod, $method));
			}
		}

		$this->pass();

		return $this;
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

?>
