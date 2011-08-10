<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\arguments
;

class mock extends atoum\asserter
{
	protected $mock = null;
	protected $call = null;
	protected $beforeFunctionCalls = array();
	protected $afterFunctionCalls = array();
	protected $beforeMethodCalls = array();
	protected $afterMethodCalls = array();

	public function reset()
	{
		if ($this->mock !== null)
		{
			$this->mock->getMockController()->resetCalls();
		}

		return $this;
	}

	public function getCall()
	{
		return ($this->call === null ? null : clone $this->call);
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

	public function beforeMethodCall($methodName)
	{
		$this->mockIsSet()->beforeMethodCalls[] = $beforeMethodCall = new mock\call\mock($this, $this->mock, $methodName);

		return $beforeMethodCall;
	}

	public function getBeforeMethodCalls()
	{
		return $this->beforeMethodCalls;
	}

	public function withAnyMethodCallsBefore()
	{
		$this->beforeMethodCalls = array();

		return $this;
	}

	public function afterMethodCall($methodName)
	{
		$this->mockIsSet()->afterMethodCalls[] = $afterMethodCall = new mock\call\mock($this, $this->mock, $methodName);

		return $afterMethodCall;
	}

	public function getAfterMethodCalls()
	{
		return $this->afterMethodCalls;
	}

	public function withAnyMethodCallsAfter()
	{
		$this->afterMethodCalls = array();

		return $this;
	}

	public function beforeFunctionCall($functionName, test\adapter $adapter)
	{
		$this->mockIsSet()->beforeFunctionCalls[] = $beforeFunctionCall = new mock\call\adapter($this, $adapter, $functionName);

		return $beforeFunctionCall;
	}

	public function getBeforeFunctionCalls()
	{
		return $this->beforeFunctionCalls;
	}

	public function withAnyFunctionCallsBefore()
	{
		$this->beforeFunctionCalls = array();

		return $this;
	}

	public function afterFunctionCall($functionName, test\adapter $adapter)
	{
		$this->mockIsSet()->afterFunctionCalls[] = $afterFunctionCall = new mock\call\adapter($this, $adapter, $functionName);

		return $afterFunctionCall;
	}

	public function getAfterFunctionCalls()
	{
		return $this->afterFunctionCalls;
	}

	public function withAnyFunctionCallsAfter()
	{
		$this->afterFunctionCalls = array();

		return $this;
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

	public function call($function)
	{
		if ($this->mockIsSet()->call === null)
		{
			$this->call = new php\call($function, null, $this->mock);
		}
		else
		{
			$this->call
				->setFunction($function)
				->setObject($this->mock)
				->unsetArguments()
			;
		}

		return $this;
	}

	public function withArguments()
	{
		$this->calledMethodNameIsSet()->call->setArguments(func_get_args());

		return $this;
	}

	public function withAnyArguments()
	{
		$this->calledMethodNameIsSet()->call->unsetArguments();

		return $this;
	}

	public function once($failMessage = null)
	{
		$this->assertOnBeforeAndAfterCalls($calls = $this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->call->getFunction(), $this->call->getArguments()));

		if (($callsNumber = sizeof($calls)) === 1)
		{
			$this->pass();
		}
		else
		{
			$this->fail(
				$failMessage !== null
				? $failMessage
				: sprintf(
						$this->getLocale()->__(
							'method %s is called %d time instead of 1',
							'method %s is called %d times instead of 1',
							$callsNumber
						),
						$this->call,
						$callsNumber
					) . $this->getCallsAsString()
			);
		}

		return $this;
	}

	public function atLeastOnce($failMessage = null)
	{
		$this->assertOnBeforeAndAfterCalls($calls = $this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->call->getFunction(), $this->call->getArguments()));

		if (($callsNumber = sizeof($calls)) >= 1)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('method %s is called 0 time'), $this->call) . $this->getCallsAsString());
		}

		return $this;
	}

	public function exactly($number, $failMessage = null)
	{
		$this->assertOnBeforeAndAfterCalls($calls = $this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->call->getFunction(), $this->call->getArguments()));

		if (($callsNumber = sizeof($calls)) == $number)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'method %s is called %d time instead of %d',
						'method %s is called %d times instead of %d',
						$callsNumber
					),
					$this->call,
					$callsNumber,
					$number
				) . $this->getCallsAsString()
			);
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
		if ($this->mockIsSet()->call === null)
		{
			throw new exceptions\logic('Called method is undefined');
		}

		return $this;
	}

	protected function assertOnBeforeAndAfterCalls($calls)
	{
		if (sizeof($calls) > 0)
		{
			foreach ($this->beforeMethodCalls as $beforeMethodCall)
			{
				$firstCall = $beforeMethodCall->getFirstCall();

				if ($firstCall === null)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s is not called'), $beforeMethodCall));
				}

				if (key($calls) > $firstCall)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s is not called before method %s'), $this->call, $beforeMethodCall));
				}

				$this->pass();
			}

			foreach ($this->beforeFunctionCalls as $beforeFunctionCall)
			{
				$firstCall = $beforeFunctionCall->getFirstCall();

				if ($firstCall === null)
				{
					$this->fail(sprintf($this->getLocale()->_('function %s is not called'), $beforeFunctionCall));
				}

				if (key($calls) > $firstCall)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s is not called before function %s'), $$this->call, $beforeFunctionCall));
				}

				$this->pass();
			}

			foreach ($this->afterMethodCalls as $afterMethodCall)
			{
				$lastCall = $afterMethodCall->getLastCall();

				if ($lastCall === null)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s is not called'), $afterMethodCall));
				}

				if (key($calls) < $lastCall)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s is not called after method %s'), $this->call, $afterMethodCall));
				}

				$this->pass();
			}

			foreach ($this->afterFunctionCalls as $afterFunctionCall)
			{
				$lastCall = $afterFunctionCall->getLastCall();

				if ($lastCall === null)
				{
					$this->fail(sprintf($this->getLocale()->_('function %s is not called'), $afterFunctionCall));
				}

				if (key($calls) < $lastCall)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s is not called after function %s'), $this->call, $afterFunctionCall));
				}

				$this->pass();
			}
		}

		return $this;
	}

	protected function getCallsAsString()
	{
		$string = '';

		if (($calls  = $this->mock->getMockController()->getCalls($this->call->getFunction())) !== null)
		{
			$format = '[%' . strlen((string) sizeof($calls)) . 's] %s';

			$phpCalls = array();

			foreach (array_values($calls) as $call => $arguments)
			{
				$phpCalls[] = sprintf($format, $call + 1, new php\call($this->call->getFunction(), $arguments, $this->mock));
			}

			$string = PHP_EOL . join(PHP_EOL, $phpCalls);
		}

		return $string;
	}
}

?>
