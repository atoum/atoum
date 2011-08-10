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
		$this->assertOnBeforeAndAfterCalls($calls = $this->calledFunctionNameIsSet()->adapter->getCalls($this->call->getFunction(), $this->call->getArguments()));

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
							'function %s is called %d time instead of 1',
							'function %s is called %d times instead of 1',
							$callsNumber
						),
						$this->call,
						$callsNumber
					) .  $this->getCallsAsString()
			);
		}

		return $this;
	}

	public function atLeastOnce($failMessage = null)
	{
		$this->assertOnBeforeAndAfterCalls($calls = $this->calledFunctionNameIsSet()->adapter->getCalls($this->call->getFunction(), $this->call->getArguments()));

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
		$this->assertOnBeforeAndAfterCalls($calls = $this->calledFunctionNameIsSet()->adapter->getCalls($this->call->getFunction(), $this->call->getArguments()));

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

	protected function calledFunctionNameIsSet()
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

		if (($calls  = $this->adapter->getCalls($this->call->getFunction())) !== null)
		{
			$format = '[%' . strlen((string) sizeof($calls)) . 's] %s';

			$phpCalls = array();

			foreach (array_values($calls) as $call => $arguments)
			{
				$phpCalls[] = sprintf($format, $call + 1, new php\call($this->call->getFunction(), $arguments));
			}

			$string = PHP_EOL . join(PHP_EOL, $phpCalls);
		}

		return $string;
	}
}

?>
