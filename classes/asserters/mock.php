<?php

namespace mageekguy\atoum\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserters,
	\mageekguy\atoum\exceptions
;

class mock extends atoum\asserter
{
	protected $mock = null;

	public function reset()
	{
		$this->mock->getMockController()->resetCalls();

		return $this;
	}

	public function setWith($mock, $label = null)
	{
		$this->mock = $mock;

		if ($this->mock instanceof \mageekguy\atoum\mock\aggregator === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a mock'), $this->toString($this->mock)));
		}
		else
		{
			$this->pass();
		}

		return $this->setLabel($label);
	}

	public function getMock()
	{
		return $this->mock;
	}

	public function wasCalled($failMessage = null)
	{
		if (sizeof($this->mockIsSet()->mock->getMockController()->getCalls()) > 0)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not called'), get_class($this->mock)));
		}
	}

	public function wasNotCalled($failMessage = null)
	{
		if (sizeof($this->mockIsSet()->mock->getMockController()->getCalls()) <= 0)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is called'), get_class($this->mock)));
		}
	}

	public function call($method, array $args = null, $failMessage = null)
	{
		$calls = $this->mockIsSet()->mock->getMockController()->getCalls($method);

		if (sizeof($calls) <= 0)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('method %s::%s() is not called'), get_class($this->mock), $method));
		}
		else if ($args !== null && in_array($args, $calls) === false)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->__('method %s::%s() is not called with this argument', 'method %s::%s() is not called with these arguments', sizeof($args)), get_class($this->mock), $method));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function notCall($method, array $args = null, $failMessage = null)
	{
		$calls = $this->mockIsSet()->mock->getMockController()->getCalls($method);

		if (sizeof($calls) <= 0)
		{
			$this->pass();
		}
		else if ($args === null)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('method %s::%s() is called'), get_class($this->mock), $method));
		}
		else if (in_array($args, $calls) === true)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->__('method %s::%s() is called with this argument', 'method %s::%s() is called with these arguments', sizeof($args)), get_class($this->mock), $method));
		}
		else
		{
			$this->pass();
		}

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
