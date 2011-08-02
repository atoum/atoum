<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\arguments
;

class mock extends atoum\asserter
{
	protected $mock = null;
	protected $calledMethodName = null;
	protected $calledMethodArguments = null;
	protected $beforeFunctionCalls = array();
	protected $afterFunctionCalls = array();
	protected $beforeMethodCalls = array();
	protected $afterMethodCalls = array();
	protected $argumentsDecorator = null;

	public function __construct(asserter\generator $generator)
	{
		parent::__construct($generator);

		$this->setArgumentsDecorator(new arguments\decorator());
	}

	public function setArgumentsDecorator(arguments\decorator $decorator)
	{
		$this->argumentsDecorator = $decorator;

		return $this;
	}

	public function getArgumentsDecorator()
	{
		return $this->argumentsDecorator;
	}

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

	public function withAnyArguments()
	{
		$this->calledMethodNameIsSet()->calledMethodArguments = null;

		return $this;
	}

	public function once($failMessage = null)
	{
		$this->assertOnBeforeAndAfterMethodCall($calls = $this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->calledMethodName, $this->calledMethodArguments));

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
							'method %s::%s(%s) is called %d time instead of 1',
							'method %s::%s(%s) is called %d times instead of 1',
							$callsNumber
						),
						get_class($this->mock),
						$this->calledMethodName,
						$this->getMethodArgumentsAsString(),
						$callsNumber
					)
			);
		}

		return $this;
	}

	public function atLeastOnce($failMessage = null)
	{
		$this->assertOnBeforeAndAfterMethodCall($calls = $this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->calledMethodName, $this->calledMethodArguments));

		if (($callsNumber = sizeof($calls)) < 1)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('method %s::%s(%s) is called 0 time'), get_class($this->mock), $this->calledMethodName, $this->getMethodArgumentsAsString()));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function exactly($number, $failMessage = null)
	{
		$this->assertOnBeforeAndAfterMethodCall($calls = $this->calledMethodNameIsSet()->mock->getMockController()->getCalls($this->calledMethodName, $this->calledMethodArguments));

		if (($callsNumber = sizeof($calls)) != $number)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'method %s::%s(%s) is called %d time instead of %d',
						'method %s::%s(%s) is called %d times instead of %d',
						$callsNumber
					),
					get_class($this->mock),
					$this->calledMethodName,
					$this->getMethodArgumentsAsString(),
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

	protected function assertOnBeforeAndAfterMethodCall($calls)
	{
		if (sizeof($calls) > 0)
		{
			foreach ($this->beforeMethodCalls as $beforeMethodCall)
			{
				$firstCall = $beforeMethodCall->getFirstCall();

				if ($firstCall === null)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s::%s() is not called'), get_class($beforeMethodCall->getMockAggregator()), $beforeMethodCall->getMethodName()));
				}

				if (key($calls) > $firstCall)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s::%s() is not called before method %s::%s()'), get_class($this->mock), $this->calledMethodName, get_class($beforeMethodCall->getMockAggregator()), $beforeMethodCall->getMethodName()));
				}

				$this->pass();
			}

			foreach ($this->afterMethodCalls as $afterMethodCall)
			{
				$lastCall = $afterMethodCall->getLastCall();

				if ($lastCall === null)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s::%s() is not called'), get_class($afterMethodCall->getMockAggregator()), $afterMethodCall->getMethodName()));
				}

				if (key($calls) < $lastCall)
				{
					$this->fail(sprintf($this->getLocale()->_('method %s::%s() is not called after method %s::%s()'), get_class($this->mock), $this->calledMethodName, get_class($afterMethodCall->getMockAggregator()), $afterMethodCall->getMethodName()));
				}

				$this->pass();
			}
		}

		return $this;
	}

	protected function getMethodArgumentsAsString()
	{
		$arguments = '';

		if ($this->calledMethodArguments !== null && sizeof($this->calledMethodArguments) > 0)
		{
			$arguments = array();

			foreach ($this->calledMethodArguments as $argument)
			{
				switch ($type = gettype($argument))
				{
					case 'boolean':
						$arguments[] = 'boolean(' . ($argument ? 'TRUE' : 'FALSE')  . ')';
						break;

					case 'integer':
						$arguments[] = 'integer(' . $argument . ')';
						break;

					case 'double':
						$arguments[] = 'float(' . $argument . ')';
						break;

					case 'string':
						$arguments[] = 'string(' . strlen($argument) . ') "' . $argument . '"';
						break;

					case 'array':
						$arguments[] = 'array(' . ($size = sizeof($argument)) . ') {' . ($size <= 0 ? '' : '...') . '}';
						break;

					case 'object':
						$arguments[] = get_class($argument);
						break;

					case 'resource':
						ob_start();
						var_dump($argument);
						$arguments[] = ob_get_clean();
						break;

					case 'NULL':
						$arguments[] = $type;
						break;

					default:
						$arguments[] = $type;
				}
			}

			$arguments = join(', ', $arguments);
		}

		return $arguments;
	}
}

?>
