<?php

namespace mageekguy\atoum\asserters;

class mock extends \mageekguy\atoum\asserter
{
	protected $mock = null;

	public function setWith($mock)
	{
		$this->mock = $mock;

		if ($this->mock instanceof \mageekguy\atoum\mock\aggregator === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not a mock'), $this->mock));
		}
		else
		{
			$this->pass();

			return $this;
		}
	}

	public function getMock()
	{
		return $this->mock;
	}

	public function wasCalled($failMessage = null)
	{
		sizeof($this->mockIsSet()->mock->getMockController()->getCalls()) > 0 ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not called'), get_class($this->mock)));

		return $this;
	}

	public function callMethod($method, $failMessage = null)
	{
		sizeof($this->mockIsSet()->mock->getMockController()->getCalls($method)) > 0 ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('Method %s is not called'), $method));

		return $this;
	}

	protected function mockIsSet()
	{
		if ($this->mock === null)
		{
			throw new \logicException('Mock is undefined');
		}

		return $this;
	}
}

?>
