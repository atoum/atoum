<?php

namespace mageekguy\atoum\asserters\call;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class adapter extends asserters\call
{
	protected $adapterAsserter;
	protected $beforeMethodCalls = array();
	protected $afterMethodCalls = array();
	protected $beforeFunctionCalls = array();
	protected $afterFunctionCalls = array();

	public function __construct(asserters\adapter $adapterAsserter, asserter\generator $generator = null)
	{
		parent::__construct($generator ?: $adapterAsserter->getGenerator());

		$this->adapterAsserter = $adapterAsserter;
	}

	public function __call($method, $arguments)
	{
		try
		{
			return call_user_func_array(array($this->adapterAsserter, $method), $arguments);
		}
		catch (exceptions\logic\invalidArgument $e)
		{
			return parent::__call($method, $arguments);
		}
	}

	public function getAdapterAsserter()
	{
		return $this->adapterAsserter;
	}

	public function getArguments()
	{
		if ($this->callIsSet()->arguments === null)
		{
			$this->arguments = new arguments($this);
		}

		return $this->arguments->setWith($this->call->getObject());
	}

	public function beforeMethodCall($methodName, atoum\mock\aggregator $mock)
	{
		$this->beforeMethodCalls[] = $beforeMethodCall = new asserters\adapter\call\mock($this, $mock, $methodName);

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
		$this->afterMethodCalls[] = $afterMethodCall = new asserters\adapter\call\mock($this, $mock, $methodName);

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
		$this->beforeFunctionCalls[] = $beforeFunctionCall = new asserters\adapter\call\adapter($this, $this->adapterAsserter->getAdapter(), $methodName);

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
		$this->afterFunctionCalls[] = $afterFunctionCall = new asserters\adapter\call\adapter($this, $this->adapterAsserter->getAdapter(), $methodName);

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

	public function exactly($number, $failMessage = null)
	{
		$this->assertOnBeforeAndAfterCalls($calls = $this->callIsSet()->call->getObject()->getCalls($this->call->getFunction(), $this->call->getArguments()));

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

		return $this->adapterAsserter;
	}

	public function atLeastOnce($failMessage = null)
	{
		$this->assertOnBeforeAndAfterCalls($calls = $this->callIsSet()->call->getObject()->getCalls($this->call->getFunction(), $this->call->getArguments()));

		if (($callsNumber = sizeof($calls)) >= 1)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('function %s is called 0 time'), $this->call) . $this->getCallsAsString());
		}

		return $this->adapterAsserter;
	}

	protected function getCallsAsString()
	{
		$string = '';

		if (($calls  = $this->call->getObject()->getCalls($this->call->getFunction())) !== null)
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

				if (key($calls) > $firstCall)
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

				if (key($calls) > $firstCall)
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

				if (key($calls) < $lastCall)
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

				if (key($calls) < $lastCall)
				{
					$this->fail(sprintf($this->getLocale()->_('function %s is not called after function %s'), $this->call, $afterFunctionCall));
				}

				$this->pass();
			}
		}

		return $this;
	}
}