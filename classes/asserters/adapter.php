<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class adapter extends atoum\asserter
{
	protected $adapter = null;
	protected $calledFunctionName = null;
	protected $calledFunctionArguments = null;
	protected $beforeMethodCalls = array();
	protected $afterMethodCalls = array();
	protected $beforeFunctionCalls = array();
	protected $afterFunctionCalls = array();

	public function setWith($adapter)
	{
		$this->adapter = $adapter;

		if ($this->adapter instanceof \mageekguy\atoum\test\adapter === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a test adapter'), $this->getTypeOf($this->adapter)));
		}
		else
		{
			$this->pass();
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

	public function getTestedFunctionName()
	{
		return $this->calledFunctionName;
	}

	public function getTestedFunctionArguments()
	{
		return $this->calledFunctionArguments;
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

	public function call($method)
	{
		$this->adapterIsSet()->calledFunctionName = $method;

		$this->calledFunctionArguments = null;

		return $this;
	}

	public function withArguments()
	{
		$this->calledFunctionNameIsSet()->calledFunctionArguments = func_get_args();

		return $this;
	}

	public function withAnyArguments()
	{
		$this->calledFunctionNameIsSet()->calledFunctionArguments = null;

		return $this;
	}

	public function once($failMessage = null)
	{
		$callNumber = sizeof($this->calledFunctionNameIsSet()->adapter->getCalls($this->calledFunctionName, $this->calledFunctionArguments));

		if ($callNumber !== 1)
		{
			$this->fail(
				$failMessage !== null
				?  $failMessage
				:  sprintf(
						$this->getLocale()->__(
							'function %s() is called %d time instead of 1',
							'function %s() is called %d times instead of 1',
							$callNumber
						),
						$this->calledFunctionName,
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
		$callNumber = sizeof($this->calledFunctionNameIsSet()->adapter->getCalls($this->calledFunctionName, $this->calledFunctionArguments));

		if ($callNumber < 1)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('function %s() is called 0 time'), $this->calledFunctionName));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function exactly($number, $failMessage = null)
	{
		$callNumber = sizeof($this->calledFunctionNameIsSet()->adapter->getCalls($this->calledFunctionName, $this->calledFunctionArguments));

		if ($number != $callNumber)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'function %s() is called %d time instead of %d',
						'function %s() is called %d times instead of %d',
						$callNumber
					),
					$this->calledFunctionName,
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
		$callNumber = sizeof($this->calledFunctionNameIsSet()->adapter->getCalls($this->calledFunctionName, $this->calledFunctionArguments));

		if ($callNumber != 0)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'function %s() is called %d time instead of 0',
						'function %s() is called %d times instead of 0',
						$callNumber
					),
					$this->calledFunctionName,
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

	protected function adapterIsSet()
	{
		if ($this->adapter === null)
		{
			throw new exceptions\logic('Adapter is undefined');
		}

		return $this;
	}

	protected function calledFunctionNameIsSet()
	{
		if ($this->adapterIsSet()->calledFunctionName === null)
		{
			throw new exceptions\logic('Called function is undefined');
		}

		return $this;
	}
}

?>
