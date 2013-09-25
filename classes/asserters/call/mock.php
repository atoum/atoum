<?php

namespace mageekguy\atoum\asserters\call;

use
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class mock extends asserters\call
{
	protected $mockAsserter;
	protected $beforeMethodCalls = array();
	protected $afterMethodCalls = array();
	protected $beforeFunctionCalls = array();
	protected $afterFunctionCalls = array();

	public function __construct(asserters\mock $mockAsserter, asserter\generator $generator = null)
	{
		parent::__construct($generator ?: $mockAsserter->getGenerator());

		$this->mockAsserter = $mockAsserter;
	}

	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->mockAsserter, $method), $arguments);
	}

	public function getMockAsserter()
	{
		return $this->mockAsserter;
	}

	public function beforeMethodCall($methodName)
	{
		$this->mockIsSet()->beforeMethodCalls[] = $beforeMethodCall = new asserters\mock\call\mock($this, $this->mockIsSet()->mockAsserter->getMock(), $methodName);

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
		$this->mockIsSet()->afterMethodCalls[] = $afterMethodCall = new asserters\mock\call\mock($this, $this->mockIsSet()->mockAsserter->getMock(), $methodName);

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
		$this->mockIsSet()->beforeFunctionCalls[] = $beforeFunctionCall = new asserters\mock\call\adapter($this, $adapter, $functionName);

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
		$this->mockIsSet()->afterFunctionCalls[] = $afterFunctionCall = new asserters\mock\call\adapter($this, $adapter, $functionName);

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
		$calls = $this->assertOnBeforeAndAfterCalls();

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

	public function atLeastOnce($failMessage = null)
	{
		$calls = $this->assertOnBeforeAndAfterCalls();

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


	protected function assertOnBeforeAndAfterCalls()
	{
		$calls = $this->callIsSet()->mockAsserter->getMock()->getMockController()->getCalls($this->call->getFunction(), $this->call->getArguments(), $this->call->isIdentical());

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
					$this->fail(sprintf($this->getLocale()->_('method %s is not called'), $beforeFunctionCall));
				}

				if (key($calls) > $firstCall)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s is not called before function %s'), $this->call, $beforeFunctionCall));
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
					$this->fail(sprintf($this->getLocale()->_('method %s is not called'), $afterFunctionCall));
				}

				if (key($calls) < $lastCall)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s is not called after function %s'), $this->call, $afterFunctionCall));
				}

				$this->pass();
			}
		}

		$this->beforeMethodCalls = array();
		$this->afterMethodCalls = array();
		$this->beforeFunctionCalls = array();
		$this->afterFunctionCalls = array();

		return $calls;
	}

	protected function callIsSet()
	{
		if ($this->mockIsSet()->call === null)
		{
			throw new exceptions\logic('Called method is undefined');
		}

		return $this;
	}

	protected function mockIsSet()
	{
		if ($this->mockAsserter->getMock() === null)
		{
			throw new exceptions\logic('Mock is undefined');
		}

		return $this;
	}

	protected function getCallsAsString()
	{
		$string = '';

		if (($calls  = $this->mockAsserter->getMock()->getMockController()->getCalls($this->call->getFunction())) !== null)
		{
			$format = '[%' . strlen((string) sizeof($calls)) . 's] %s';

			$phpCalls = array();

			foreach (array_values($calls) as $call => $arguments)
			{
				$phpCalls[] = sprintf($format, $call + 1, new php\call($this->call->getFunction(), $arguments, $this->mockAsserter->getMock()));
			}

			$string = PHP_EOL . join(PHP_EOL, $phpCalls);
		}

		return $string;
	}
}
