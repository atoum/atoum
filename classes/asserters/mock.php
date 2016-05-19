<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters\adapter,
	mageekguy\atoum\test\adapter\call\decorators
;

class mock extends adapter
{
	public function setWith($mock)
	{
		if ($mock instanceof atoum\mock\aggregator === false)
		{
			$this->fail($this->_('%s is not a mock', $this->getTypeOf($mock)));
		}
		else
		{
			parent::setWith($mock->getMockController());

			$this->call->setDecorator(new decorators\addClass($this->adapter->getMockClass()));
		}

		return $this;
	}

	public function receive($function)
	{
		return $this->call($function);
	}

	public function wasCalled($failMessage = null)
	{
		if ($this->adapterIsSet()->adapter->getCallsNumber() > 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not called', $this->adapter->getMockClass()));
		}

		return $this;
	}

	public function wasNotCalled($failMessage = null)
	{
		if ($this->adapterIsSet()->adapter->getCallsNumber() <= 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is called', $this->adapter->getMockClass()));
		}

		return $this;
	}

	protected function adapterIsSet()
	{
		try
		{
			return parent::adapterIsSet();
		}
		catch (adapter\exceptions\logic $exception)
		{
			throw new mock\exceptions\logic('Mock is undefined');
		}
	}

	protected function callIsSet()
	{
		try
		{
			return parent::callIsSet();
		}
		catch (adapter\exceptions\logic $exception)
		{
			throw new mock\exceptions\logic('Call is undefined');
		}
	}
}
