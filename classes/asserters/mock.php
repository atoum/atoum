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
	protected $calledMethodName = null;
	protected $calledMethodArguments = null;

	public function getTestedMethodName()
	{
		return $this->calledMethodName;
	}

	public function getTestedMethodArguments()
	{
		return $this->calledMethodArguments;
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

	public function call($method)
	{
		$this->mockIsSet()->calledMethodName = $method;

		$this->calledMethodArguments = null;

		return $this;
	}

	public function withArguments()
	{
		$this->calledMethodNameIsSet()->calledMethodArguments = func_get_args();

		return $this;
	}

	public function once($failMessage = null)
	{
		$callNumber = sizeof($this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->calledMethodName, $this->calledMethodArguments));

		if ($callNumber !== 1)
		{
			$this->fail(
				$failMessage !== null
				?  $failMessage
				:  sprintf(
						$this->getLocale()->__(
							'method %s::%s() is called %d time instead of 1',
							'method %s::%s() is called %d times instead of 1',
							$callNumber
						),
						get_class($this->mock),
						$this->calledMethodName,
						$callNumber
					)
			);
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function atLeastOnce($failMessage = null)
	{
		$callNumber = sizeof($this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->calledMethodName, $this->calledMethodArguments));

		if ($callNumber < 1)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('method %s::%s() is called 0 time'), get_class($this->mock), $this->calledMethodName));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function exactly($number, $failMessage = null)
	{
		$callNumber = sizeof($this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->calledMethodName, $this->calledMethodArguments));

		if ($number != $callNumber)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'method %s::%s() is called %d time instead of %d',
						'method %s::%s() is called %d times instead of %d',
						$callNumber
					),
					get_class($this->mock),
					$this->calledMethodName,
					$callNumber,
					$number
				)
			);
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function never($failMessage = null)
	{
		$callNumber = sizeof($this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->calledMethodName, $this->calledMethodArguments));

		if ($callNumber != 0)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'method %s::%s() is called %d time instead of 0',
						'method %s::%s() is called %d times instead of 0',
						$callNumber
					),
					get_class($this->mock),
					$this->calledMethodName,
					$callNumber
				)
			);
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

	protected function calledMethodNameIsSet()
	{
		if ($this->mockIsSet()->calledMethodName === null)
		{
			throw new exceptions\logic('Called method is undefined');
		}

		return $this;
	}
}

?>
