<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\arguments
;

class adapter extends atoum\asserter
{
	protected $adapter = null;
	protected $call = null;
	protected $beforeMethodCalls = array();
	protected $afterMethodCalls = array();
	protected $beforeFunctionCalls = array();
	protected $afterFunctionCalls = array();

	public function __construct(asserter\generator $generator)
	{
		parent::__construct($generator);
	}

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
			$this->call = new php\call($function);
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
		$this->calledFunctionNameIsSet()->call->setArguments(func_get_args());

		return $this;
	}

	public function withAnyArguments()
	{
		$this->calledFunctionNameIsSet()->call->unsetArguments();

		return $this;
	}

	public function once($failMessage = null)
	{
		$callNumber = sizeof($this->calledFunctionNameIsSet()->adapter->getCalls($this->call->getFunction(), $this->call->getArguments()));

		if ($callNumber !== 1)
		{
			$this->fail(
				$failMessage !== null
				?  $failMessage
				:  sprintf(
						$this->getLocale()->__(
							'function %s is called %d time instead of 1',
							'function %s is called %d times instead of 1',
							$callNumber
						),
						$this->call,
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
		$callNumber = sizeof($this->calledFunctionNameIsSet()->adapter->getCalls($this->call->getFunction(), $this->call->getArguments()));

		if ($callNumber >= 1)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('function %s is called 0 time'), $this->call));
		}

		return $this;
	}

	public function exactly($number, $failMessage = null)
	{
		$callNumber = sizeof($this->calledFunctionNameIsSet()->adapter->getCalls($this->call->getFunction(), $this->call->getArguments()));

		if ($number == $callNumber)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'function %s is called %d time instead of %d',
						'function %s is called %d times instead of %d',
						$callNumber
					),
					$this->call,
					$callNumber,
					$number
				)
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

	protected function calledFunctionNameIsSet()
	{
		if ($this->adapterIsSet()->call === null)
		{
			throw new exceptions\logic('Called function is undefined');
		}

		return $this;
	}
}

?>
