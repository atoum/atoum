<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test,
	mageekguy\atoum\tools\arguments
;

class adapter extends atoum\asserter
{
	protected $adapter = null;
	protected $call = null;
	protected $identicalCall = false;

	protected $beforeMethodCalls = array();
	protected $afterMethodCalls = array();
	protected $beforeFunctionCalls = array();
	protected $afterFunctionCalls = array();

	public function setWith($adapter)
	{
		$this->adapter = $adapter;

		if ($this->adapter instanceof \mageekguy\atoum\test\adapter)
		{
			$this->pass();
		}
		else
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a test adapter'), $this->getTypeOf($this->adapter)));
		}

		return $this;
	}

	public function reset()
	{
		if ($this->adapter !== null)
		{
			$this->adapter->resetCalls();
		}

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function beforeMethodCall($methodName, atoum\mock\aggregator $mock)
	{
		$this->adapterIsSet()->beforeMethodCalls[] = $beforeMethodCall = new adapter\call\mock($this, $mock, $methodName);

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

	public function afterMethodCall($methodName, atoum\mock\aggregator $mock)
	{
		$this->adapterIsSet()->afterMethodCalls[] = $afterMethodCall = new adapter\call\mock($this, $mock, $methodName);

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

	public function beforeFunctionCall($methodName)
	{
		$this->adapterIsSet()->beforeFunctionCalls[] = $beforeFunctionCall = new adapter\call\adapter($this, $this->adapter, $methodName);

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

	public function afterFunctionCall($methodName)
	{
		$this->adapterIsSet()->afterFunctionCalls[] = $afterFunctionCall = new adapter\call\adapter($this, $this->adapter, $methodName);

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

	public function call($function)
	{
		if ($this->adapterIsSet()->call === null)
		{
			$this->call = new test\adapter\call($function);
		}
		else
		{
			$this->call
					->setFunction($function)
					->unsetArguments()
			;
		}

		return $this;
	}

	public function getCall()
	{
		return ($this->call === null ? null : clone $this->call);
	}

	public function withArguments()
	{
		$this->callIsSet()->call->setArguments(func_get_args());
		$this->identicalCall = false;

		return $this;
	}

	public function withIdenticalArguments()
	{
		$this->callIsSet()->call->setArguments(func_get_args())->identical();
		$this->identicalCall = true;

		return $this;
	}

	public function withAnyArguments()
	{
		$this->callIsSet()->call->unsetArguments();
		$this->identicalCall = false;

		return $this;
	}

	public function withoutAnyArgument()
	{
		$this->callIsSet()->call->setArguments(array());
		$this->identicalCall = false;

		return $this;
	}

	public function once($failMessage = null)
	{
		return $this->exactly(1, $failMessage);
	}

	public function twice($failMessage = null)
	{
		return $this->exactly(2, $failMessage);
	}

	public function thrice($failMessage = null)
	{
		return $this->exactly(3, $failMessage);
	}

	public function atLeastOnce($failMessage = null)
	{
		if ($this->callIsSet()->identicalCall === false)
		{
			$calls = $this->adapter->getCallsEqualTo($this->call);
		}
		else
		{
			$calls = $this->adapter->getCallsIdenticalTo($this->call);
		}

		$this->assertOnBeforeAndAfterCalls($calls);

		if (($callsNumber = sizeof($calls)) >= 1)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('function %s is called 0 time'), $this->call) . $this->getCallsAsString());
		}

		return $this;
	}

	public function exactly($number, $failMessage = null)
	{
		if ($this->callIsSet()->identicalCall === false)
		{
			$calls = $this->adapter->getCallsEqualTo($this->call);
		}
		else
		{
			$calls = $this->adapter->getCallsIdenticalTo($this->call);
		}

		$this->assertOnBeforeAndAfterCalls($calls);

		if (($callsNumber = sizeof($calls)) === $number)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'function %s is called %d time instead of %d',
						'function %s is called %d times instead of %d',
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
		return $this->exactly(0, $failMessage);
	}

	protected function adapterIsSet()
	{
		if ($this->adapter === null)
		{
			throw new exceptions\logic('Adapter is undefined');
		}

		return $this;
	}

	protected function callIsSet()
	{
		if ($this->adapterIsSet()->call === null)
		{
			throw new exceptions\logic('Called function is undefined');
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
					$this->fail(sprintf($this->getLocale()->_('function %s is not called'), $beforeMethodCall));
				}

				$lastCall = key($this->identicalCall === false ? $this->adapter->getLastCallEqualTo($this->call) : $this->adapter->getLastCallIdenticalTo($this->call));

				if ($lastCall > $firstCall)
				{
					$this->fail(sprintf($this->getLocale()->_('function %s is not called before method %s'), $this->call, $beforeMethodCall));
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

				$lastCall = key($this->identicalCall === false ? $this->adapter->getLastCallEqualTo($this->call) : $this->adapter->getLastCallIdenticalTo($this->call));

				if ($lastCall > $firstCall)
				{
					$this->fail(sprintf($this->getLocale()->_('function %s is not called before function %s'), $this->call, $beforeFunctionCall));
				}

				$this->pass();
			}

			foreach ($this->afterMethodCalls as $afterMethodCall)
			{
				$lastCall = $afterMethodCall->getLastCall();

				if ($lastCall === null)
				{
					$this->fail(sprintf($this->getLocale()->_('function %s is not called'), $afterMethodCall));
				}

				$firstCall = key($this->identicalCall === false ? $this->adapter->getFirstCallEqualTo($this->call) : $this->adapter->getFirstCallIdenticalTo($this->call));

				if ($firstCall < $lastCall)
				{
					$this->fail(sprintf($this->getLocale()->_('function %s is not called after method %s'), $this->call, $afterMethodCall));
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

				$firstCall = key($this->identicalCall === false ? $this->adapter->getFirstCallEqualTo($this->call) : $this->adapter->getFirstCallIdenticalTo($this->call));

				if ($firstCall < $lastCall)
				{
					$this->fail(sprintf($this->getLocale()->_('function %s is not called after function %s'), $this->call, $afterFunctionCall));
				}

				$this->pass();
			}
		}

		return $this;
	}

	protected function getCallsAsString()
	{
		$referenceCall = clone $this->call;

		$calls = $this->adapter->getCallsEqualTo($referenceCall->unsetArguments());

		return (sizeof($calls) <= 0 ? '' : PHP_EOL . rtrim($calls));
	}
}
