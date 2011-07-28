<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class mock extends atoum\asserter
{
	protected $mock = null;
	protected $calledMethodName = null;
	protected $calledMethodArguments = null;
	protected $beforeCall = null;
	protected $afterCall = null;

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

	public function beforeCallTo($methodName)
	{
		$this->mockIsSet()->beforeCall = new mock\call($this, $this->mock, $methodName);

		return $this->beforeCall;
	}

	public function afterCallTo($methodName)
	{
		$this->mockIsSet()->afterCall = new mock\call($this, $this->mock, $methodName);

		return $this->afterCall;
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
		$this->assertOnBeforeAndAfterCallTo($calls = $this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->calledMethodName, $this->calledMethodArguments));

		if (($callsNumber = sizeof($calls)) !== 1)
		{
			$this->fail(
				$failMessage !== null
				?  $failMessage
				:  sprintf(
						$this->getLocale()->__(
							'method %s::%s() is called %d time instead of 1',
							'method %s::%s() is called %d times instead of 1',
							$callsNumber
						),
						get_class($this->mock),
						$this->calledMethodName,
						$callsNumber
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
		$this->assertOnBeforeAndAfterCallTo($calls = $this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->calledMethodName, $this->calledMethodArguments));

		if (($callsNumber = sizeof($calls)) < 1)
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
		$this->assertOnBeforeAndAfterCallTo($calls = $this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->calledMethodName, $this->calledMethodArguments));

		if (($callsNumber = sizeof($calls)) != $number)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'method %s::%s() is called %d time instead of %d',
						'method %s::%s() is called %d times instead of %d',
						$callsNumber
					),
					get_class($this->mock),
					$this->calledMethodName,
					$callsNumber,
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
		return $this->exactly(0);
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

	protected function assertOnBeforeAndAfterCallTo($calls)
	{
		if (sizeof($calls) > 0)
		{
			if ($this->beforeCall !== null)
			{
				$beforeCall = $this->beforeCall->getFirstCall();

				if ($beforeCall === null)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s::%s() is not called'), get_class($this->beforeCall->getMockAggregator()), $this->beforeCall->getMethodName()));
				}

				if (key($calls) > $beforeCall)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s::%s() is not called before method %s::%s()'), get_class($this->mock), $this->calledMethodName, get_class($this->beforeCall->getMockAggregator()), $this->beforeCall->getMethodName()));
				}
			}

			if ($this->afterCall !== null)
			{
				$afterCall = $this->afterCall->getLastCall();

				if ($afterCall === null)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s::%s() is not called'), get_class($this->afterCall->getMockAggregator()), $this->afterCall->getMethodName()));
				}

				if (key($calls) < $afterCall)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s::%s() is not called after method %s::%s()'), get_class($this->mock), $this->calledMethodName, get_class($this->afterCall->getMockAggregator()), $this->afterCall->getMethodName()));
				}
			}
		}

		return $this;
	}
}

?>
