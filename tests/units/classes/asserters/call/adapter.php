<?php
namespace mageekguy\atoum\tests\units\asserters\call;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\asserters\call\adapter as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class adapter extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->isSubclassOf('mageekguy\atoum\asserters\call')
		;
	}

	public function test__construct()
	{
		$this
			->if($adapterAsserter = new asserters\adapter())
			->and($asserter = new testedClass($adapterAsserter))
			->then
				->object($asserter->getAdapterAsserter())->isIdenticalTo($adapterAsserter)
		;
	}

	public function testOnce()
	{
		$this
			->if($adapterAsserter = new asserters\adapter($generator = new asserter\generator()))
			->and($asserter = new testedClass($adapterAsserter))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->setWith($call = new php\call('md5', null, $adapter = new test\adapter())))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 1'), $call))
			->if($adapter->md5($firstArgument = uniqid()))
			->then
				->object($asserter->once())->isIdenticalTo($adapterAsserter)
			->if($adapter->md5($secondArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 2 times instead of 1'), $call) . PHP_EOL . '[1] ' . new php\call('md5', array($firstArgument)) . PHP_EOL . '[2] ' . new php\call('md5', array($secondArgument)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->and($adapter->md5($arg))
			->then
				->object($asserter->once())->isIdenticalTo($adapterAsserter)
			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($arg)))
		;
	}

	public function testTwice()
	{
		$this
			->if($adapterAsserter = new asserters\adapter($generator = new asserter\generator()))
			->and($asserter = new testedClass($adapterAsserter))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->and($asserter->setWith($call = new php\call('md5', null, $adapter)))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()))
			->if($adapter->md5($firstArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($firstArgument)))
			->if($adapter->md5($secondArgument = uniqid()))
			->then
				->object($asserter->twice())->isIdenticalTo($adapterAsserter)
			->if($adapter->md5($thirdArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($firstArgument)) . PHP_EOL . '[2] ' . new php\call('md5', array($secondArgument)) . PHP_EOL . '[3] ' . new php\call('md5', array($thirdArgument)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->and($adapter->md5($arg))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($arg)))
			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($arg)))
		;
	}

	public function testThrice()
	{
		$this
			->if($adapterAsserter = new asserters\adapter($generator = new asserter\generator()))
			->and($asserter = new testedClass($adapterAsserter))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->setWith($call = new php\call('md5', null, $adapter = new test\adapter())))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 3'), $asserter->getCall()))
			->if($call = new php\call('md5'))
			->and($adapter->md5($firstArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($firstArgument)))
			->if($adapter->md5($secondArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 2 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($firstArgument)) . PHP_EOL . '[2] ' . new php\call('md5', array($secondArgument)))
			->if($adapter->md5($thirdArgument = uniqid()))
			->then
				->object($asserter->thrice())->isIdenticalTo($adapterAsserter)
			->if($adapter->md5($fourthArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 4 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($firstArgument)) . PHP_EOL . '[2] ' . new php\call('md5', array($secondArgument)) . PHP_EOL . '[3] ' . new php\call('md5', array($thirdArgument)) . PHP_EOL . '[4] ' . new php\call('md5', array($fourthArgument)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->and($adapter->md5($arg))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($arg)))
			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($arg)))
		;
	}

	public function testNever()
	{
		$this
			->if($adapterAsserter = new asserters\adapter($generator = new asserter\generator()))
			->and($asserter = new testedClass($adapterAsserter))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->setWith($call = new php\call('md5', null, $adapter = new test\adapter())))
			->then
				->object($asserter->never())->isIdenticalTo($adapterAsserter)
			->if($adapter->md5($usedArg = uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($usedArg)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->then
				->object($asserter->never())->isIdenticalTo($adapterAsserter)
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($arg)))
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 2 times instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($arg)) . PHP_EOL . '[2] ' . new php\call('md5', array($arg)))
			->if($asserter->withArguments(uniqid()))
			->then
				->object($asserter->never())->isIdenticalTo($adapterAsserter)
		;
	}

	public function testAtLeastOnce()
	{
		$this
			->if($adapterAsserter = new asserters\adapter($generator = new asserter\generator()))
			->and($asserter = new testedClass($adapterAsserter))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->setWith($call = new php\call('md5', null, $adapter = new test\adapter())))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time'), $asserter->getCall()))
			->if($adapter->md5(uniqid()))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($adapterAsserter)
			->if($adapter->md5(uniqid()))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($adapterAsserter)
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time'), $asserter->getCall()))
			->if($call = new php\call('md5'))
			->and($adapter->md5($arg))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($adapterAsserter)
			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($arg)))
		;
	}

	public function testExactly()
	{
		$this
			->if($adapterAsserter = new asserters\adapter($generator = new asserter\generator()))
			->and($asserter = new testedClass($adapterAsserter))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->setWith($call = new php\call('md5', null, $adapter = new test\adapter())))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()))
			->if($call = new php\call('md5'))
			->and($adapter->md5($arg = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($arg)))
			->if($adapter->md5($otherArg = uniqid()))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($adapterAsserter)
			->if($adapter->md5($anOtherArg = uniqid()))
			->then
				->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($arg)) . PHP_EOL . '[2] ' . new php\call('md5', array($otherArg)) . PHP_EOL . '[3] ' . new php\call('md5', array($anOtherArg)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()))
			->if($adapter->md5($usedArg = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($usedArg)))
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($usedArg)) . PHP_EOL . '[2] ' . new php\call('md5', array($arg)))
			->if($adapter->md5($arg))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($adapterAsserter)
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $anAnotherLine, $asserter) { $anAnotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . new php\call('md5', array($usedArg)) . PHP_EOL . '[2] ' . new php\call('md5', array($arg)) . PHP_EOL . '[3] ' . new php\call('md5', array($arg))  . PHP_EOL . '[4] ' . new php\call('md5', array($arg)))
		;
	}

	public function testBeforeMethodCall()
	{
		$this
			->if($mock = new \mock\dummy())
			->and($asserter = new testedClass($adapterAsserter = new asserters\adapter($generator = new asserter\generator())))
			->and($asserter->setWith(new php\call(uniqid())))
			->then
				->object($asserter->beforeMethodCall('foo', $mock))->isEqualTo($beforeMethodCall = new asserters\adapter\call\mock($asserter, $mock, 'foo'))
				->array($asserter->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall))
				->object($asserter->beforeMethodCall('bar', $mock))->isEqualTo($otherBeforeMethodCall = new asserters\adapter\call\mock($asserter, $mock, 'bar'))
				->array($asserter->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall, $otherBeforeMethodCall))
		;
	}

	public function testWithAnyMethodCallsBefore()
	{
		$this
			->if($mock = new \mock\dummy())
			->and($asserter = new testedClass($adapterAsserter = new asserters\adapter($generator = new asserter\generator())))
			->then
				->array($asserter->getBeforeMethodCalls())->isEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeMethodCalls())->isEmpty()
			->if($asserter->setWith($adapter = new test\adapter()))
			->and($asserter->beforeMethodCall(uniqid(), $mock))
			->then
				->array($asserter->getBeforeMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeMethodCalls())->isEmpty()
			->if($asserter->beforeMethodCall($method1 = uniqid(), $mock)->beforeMethodCall($method2 = uniqid(), $mock))
			->then
				->array($asserter->getBeforeMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeMethodCalls())->isEmpty()
		;
	}

	public function testAfterMethodCall()
	{
		$this
			->if($mock = new \mock\dummy())
			->and($asserter = new testedClass(new asserters\adapter(new asserter\generator())))
			->then
				->object($asserter->afterMethodCall('foo', $mock))->isEqualTo($afterMethodCall = new asserters\adapter\call\mock($asserter, $mock, 'foo'))
				->array($asserter->getAfterMethodCalls())->isEqualTo(array($afterMethodCall))
				->object($asserter->afterMethodCall('bar', $mock))->isEqualTo($otherAfterMethodCall = new asserters\adapter\call\mock($asserter, $mock, 'bar'))
				->array($asserter->getAfterMethodCalls())->isEqualTo(array($afterMethodCall, $otherAfterMethodCall))
		;
	}

	public function testWithAnyMethodCallsAfter()
	{
		$this
			->if($asserter = new testedClass(new asserters\adapter(new asserter\generator())))
			->then
				->array($asserter->getAfterMethodCalls())->isEmpty()
				->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterMethodCalls())->isEmpty()
			->if($asserter->setWith($adapter = new test\adapter()))
			->and($asserter->afterMethodCall(uniqid(), new \mock\dummy()))
			->then
				->array($asserter->getAfterMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterMethodCalls())->isEmpty()
			->if($asserter
				->afterMethodCall($method1 = uniqid(), new \mock\dummy())
				->afterMethodCall($method2 = uniqid(), new \mock\dummy())
			)
			->then
				->array($asserter->getAfterMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterMethodCalls())->isEmpty()
		;
	}

	public function testBeforeFunctionCall()
	{
		$this
			->if($mock = new \mock\dummy())
			->and($asserter = new testedClass($adapterAsserter = new asserters\adapter()))
			->and($adapterAsserter->setWith($adapter = new test\Adapter()))
			->then
				->object($asserter->beforeFunctionCall('foo'))->isEqualTo($beforeFunctionCall = new asserters\adapter\call\adapter($asserter, $adapter, 'foo'))
				->array($asserter->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall))
				->object($asserter->beforeFunctionCall('bar'))->isEqualTo($otherBeforeFunctionCall = new asserters\adapter\call\adapter($asserter, $adapter, 'bar'))
				->array($asserter->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall, $otherBeforeFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsBefore()
	{
		$this
			->if($asserter = new testedClass($asserterAdapter = new asserters\adapter()))
			->and($asserterAdapter->setWith($adapter = new test\adapter()))
			->then
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
				->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
			->if($asserter->setWith(new php\call(uniqid(), null, $adapter)))
			->and($asserter->beforeFunctionCall(uniqid(), $adapter))
			->then
				->array($asserter->getBeforeFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
			->if($asserter
				->beforeFunctionCall($method1 = uniqid(), $adapter)
				->beforeFunctionCall($method2 = uniqid(), $adapter)
			)
			->then
				->array($asserter->getBeforeFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
		;
	}

	public function testAfterFunctionCall()
	{
		$this
			->if($mock = new \mock\dummy())
			->and($asserter = new testedClass($asserterAdapter = new asserters\adapter()))
			->and($asserterAdapter->setWith($adapter = new test\adapter()))
			->then
				->object($asserter->afterFunctionCall('foo', $adapter = new test\adapter()))->isEqualTo($afterFunctionCall = new asserters\adapter\call\adapter($asserter, $adapter, 'foo'))
				->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall))
				->object($asserter->afterFunctionCall('bar', $adapter))->isEqualTo($otherAfterFunctionCall = new asserters\adapter\call\adapter($asserter, $adapter, 'bar'))
				->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall, $otherAfterFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsAfter()
	{
		$this
			->if($asserter = new testedClass($adapterAsserter = new asserters\adapter()))
			->and($adapterAsserter->setWith($adapter = new test\adapter()))
			->then
				->array($asserter->getAfterFunctionCalls())->isEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterFunctionCalls())->isEmpty()
			->if($asserter->setWith(new php\call(uniqid(), null, $adapter)))
			->and($asserter->afterFunctionCall(uniqid()))
			->then
				->array($asserter->getAfterFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterFunctionCalls())->isEmpty()
			->if($asserter->afterFunctionCall($method1 = uniqid())->afterFunctionCall($method2 = uniqid()))
			->then
				->array($asserter->getAfterFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterFunctionCalls())->isEmpty()
		;
	}
}
