<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters\mock as sut
;

require_once __DIR__ . '/../../runner.php';

class dummy
{
	public function foo($arg) {}
	public function bar($arg) {}
	public function fooWithSeveralArguments($arg1, $arg2, $arg3, $arg4, $arg5) {}
}

class mock extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getMock())->isNull()
				->variable($asserter->getCall())->isNull()
				->array($asserter->getBeforeMethodCalls())->isEmpty()
				->array($asserter->getAfterMethodCalls())->isEmpty()
		;
	}

	public function testReset()
	{
		$this
			->if($mockController = new \mock\mageekguy\atoum\mock\controller())
			->and($asserter = new sut($generator = new asserter\generator()))
			->then
				->variable($asserter->getMock())->isNull()
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getMock())->isNull()
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\score()))
			->and($mock->setMockController($mockController))
			->and($this->resetMock($mockController))
			->then
				->object($asserter->getMock())->isIdenticalTo($mock)
				->object($asserter->reset())->isIdenticalTo($asserter)
				->object($asserter->getMock())->isIdenticalTo($mock)
				->mock($mockController)->call('resetCalls')->once();
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = true)
			->then
				->exception(function() use ($asserter, & $mock) { $asserter->setWith($mock = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not a mock'), $asserter->getTypeOf($mock)))
				->object($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock($adapter)))->isIdenticalTo($asserter)
				->object($asserter->getMock())->isIdenticalTo($mock)
		;
	}

	public function testWasCalled()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
				->then
					->exception(function() use ($asserter) { $asserter->wasCalled(); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Mock is undefined')
				->if($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = true)
				->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock($adapter)))
				->and($mock->getMockController()->resetCalls())
				->then
					->exception(function() use ($asserter) { $asserter->wasCalled(); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s is not called'), get_class($mock)))
					->exception(function() use ($asserter, & $failMessage) { $asserter->wasCalled($failMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($failMessage)
				->if($mock->getMockController()->{$method = __FUNCTION__} = function() {})
				->and($mock->{$method}())
				->then
					->object($asserter->wasCalled())->isIdenticalTo($asserter)
		;
	}

	public function testWasNotCalled()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->wasNotCalled(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = true)
			->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock($adapter)))
			->and($mock->getMockController()->resetCalls())
			->then
				->object($asserter->wasNotCalled())->isIdenticalTo($asserter)
			->if($mock->getMockController()->{$method = __FUNCTION__} = function() {})
			->and($mock->{$method}())
			->then
				->exception(function() use ($asserter, & $failMessage) { $asserter->wasNotCalled($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testBeforeMethodCall()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->beforeMethodCall(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->object($asserter->beforeMethodCall('foo'))->isEqualTo($beforeMethodCall = new sut\call\mock($asserter, $mock, 'foo'))
				->array($asserter->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall))
				->object($asserter->beforeMethodCall('bar'))->isEqualTo($otherBeforeMethodCall = new sut\call\mock($asserter, $mock, 'bar'))
				->array($asserter->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall, $otherBeforeMethodCall))
		;
	}

	public function testWithAnyMethodCallsBefore()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->array($asserter->getBeforeMethodCalls())->isEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeMethodCalls())->isEmpty()
			->if($asserter->setWith(new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($asserter->beforeMethodCall(uniqid()))
			->then
				->array($asserter->getBeforeMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeMethodCalls())->isEmpty()
			->if($asserter
				->beforeMethodCall(uniqid())
				->beforeMethodCall(uniqid())
			)
			->then
				->array($asserter->getBeforeMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeMethodCalls())->isEmpty()
		;
	}

	public function testAfterMethodCall()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->afterMethodCall(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->object($asserter->afterMethodCall('foo'))->isEqualTo($afterMethodCall = new sut\call\mock($asserter, $mock, 'foo'))
				->array($asserter->getAfterMethodCalls())->isEqualTo(array($afterMethodCall))
				->object($asserter->afterMethodCall('bar'))->isEqualTo($otherAfterMethodCall = new sut\call\mock($asserter, $mock, 'bar'))
				->array($asserter->getAfterMethodCalls())->isEqualTo(array($afterMethodCall, $otherAfterMethodCall))
		;
	}

	public function testWithAnyMethodCallsAfter()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->array($asserter->getAfterMethodCalls())->isEmpty()
				->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterMethodCalls())->isEmpty()
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($asserter->afterMethodCall($function = uniqid()))
			->then
				->array($asserter->getAfterMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterMethodCalls())->isEmpty()
			->if($asserter
				->afterMethodCall($function1 = uniqid())
				->afterMethodCall($function2 = uniqid())
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
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->beforeFunctionCall(uniqid(), new test\adapter()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($adapter = new test\adapter())
			->then
				->object($asserter->beforeFunctionCall('foo', $adapter))->isEqualTo($beforeFunctionCall = new sut\call\adapter($asserter, $adapter, 'foo'))
				->array($asserter->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall))
				->object($asserter->beforeFunctionCall('bar', $adapter))->isEqualTo($otherBeforeFunctionCall = new sut\call\adapter($asserter, $adapter, 'bar'))
				->array($asserter->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall, $otherBeforeFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsBefore()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
				->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($adapter = new test\adapter())
			->and($asserter->beforeFunctionCall($function = uniqid(), $adapter))
			->then
				->array($asserter->getBeforeFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
			->if($asserter
				->beforeFunctionCall($function1 = uniqid(), $adapter)
				->beforeFunctionCall($function2 = uniqid(), $adapter)
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
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->afterFunctionCall(uniqid(), new test\adapter()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($adapter = new test\adapter())
			->then
				->object($asserter->afterFunctionCall('foo', $adapter))->isEqualTo($afterFunctionCall = new sut\call\adapter($asserter, $adapter, 'foo'))
				->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall))
				->object($asserter->afterFunctionCall('bar', $adapter))->isEqualTo($otherAfterFunctionCall = new sut\call\adapter($asserter, $adapter, 'bar'))
				->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall, $otherAfterFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsAfter()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->array($asserter->getAfterFunctionCalls())->isEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterFunctionCalls())->isEmpty()
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($adapter = new test\adapter())
			->and($asserter->afterFunctionCall($function = uniqid(), $adapter))
			->then
				->array($asserter->getAfterFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterFunctionCalls())->isEmpty()
			->if($asserter
				->afterFunctionCall($function1 = uniqid(), $adapter)
				->afterFunctionCall($function2 = uniqid(), $adapter)
			)
			->then
				->array($asserter->getAfterFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterFunctionCalls())->isEmpty()
		;
	}

	public function testCall()
	{
		$this
			->if($asserter = new \mock\mageekguy\atoum\asserters\mock(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->call(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
			->if($asserter->withArguments())
			->then
				->object($asserter->getCall())->isEqualTo(new php\call($function, array(), $mock))
				->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
		;
	}

	public function testWithArguments()
	{
		$this
			->if($asserter = new \mock\mageekguy\atoum\asserters\mock(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->withArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, array(), $mock))
				->object($asserter->withArguments($arg1 = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg1), $mock))
				->object($asserter->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg1, $arg2), $mock))
		;
	}

	public function testWithAtLeastArguments()
	{
		$this
			->if($asserter = new \mock\mageekguy\atoum\asserters\mock(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->withAtLeastArguments($arguments = array(1 => uniqid())))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, $arguments, $mock))
				->object($asserter->withAtLeastArguments($arguments = array(2 => uniqid(), 5 => uniqid())))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, $arguments, $mock))
		;
	}

	public function testWithAnyArguments()
	{
		$this
			->if($asserter = new \mock\mageekguy\atoum\asserters\mock(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
			->if($asserter->withArguments($arg = uniqid()))
			->then
				->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg), $mock))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
		;
	}

	public function testWithoutAnyArgument()
	{
		$this
			->if($asserter = new \mock\mageekguy\atoum\asserters\mock(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->withoutAnyArgument())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, array(), $mock))
		;
	}

	public function testOnce()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
			->if($asserter->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()))
			->if($call = new php\call('foo', null, $mock))
			->and($mock->foo($usedArg = uniqid()))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($mock->foo($otherUsedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherUsedArg)))
			->if($mock->getMockController()->resetCalls())
			->and($asserter->withArguments($usedArg = uniqid()))
			->and($mock->foo($usedArg))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($asserter->beforeMethodCall('bar')->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()))
			->if($mock->foo(uniqid()))
				->object($asserter->once())->isIdenticalTo($asserter)
			/*
			->if($mock->getMockController()->resetCalls())
			->and($mock->bar(uniqid()))
			->and($mock->foo(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is not called before method %s'), $asserter->getCall(), current($asserter->getBeforeMethodCalls())))
			*/
			->if($mock->getMockController()->resetCalls())
			->and($mock->foo(uniqid()))
			->and($mock->bar(uniqid()))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($asserter->afterMethodCall('bar')->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()))
			/*
			->if($mock->foo(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is not called'), current($asserter->getAfterMethodCalls())))
			->if($mock->getMockController()->resetCalls())
			->and($mock->foo(uniqid()))
			->and($mock->bar(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is not called after method %s'), $asserter->getCall(), current($asserter->getAfterMethodCalls())))
			*/
			->if($mock->getMockController()->resetCalls())
			->and($mock->bar(uniqid()))
			->and($mock->foo(uniqid()))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($asserter->beforeMethodCall('bar')->withArguments($arg = 'toto')->call('foo'))
			->and($mock->foo(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls())))
		;
	}

	public function testTwice()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
			->if($asserter->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()))
			->if($call = new php\call('foo', null, $mock))
			->and($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($secondArg = uniqid()))
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
			->if($mock->foo($thirdArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($thirdArg)))
			->if($mock->getMockController()->resetCalls())
			->and($asserter->withArguments($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()))
			->if($mock->foo($usedArg))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($usedArg))
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
			->if($mock->foo($usedArg))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($usedArg)))
			->if($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($usedArg)))
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($asserter->beforeMethodCall('bar')->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()))
			->if($mock->foo($usedArg = uniqid()))
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo(uniqid()))
				->object($asserter->twice())->isIdenticalTo($asserter)
			->if($mock->getMockController()->resetCalls())
			->and($mock->foo($usedArg = uniqid()))
			->and($mock->bar(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($usedArg = uniqid()))
			->and($mock->bar(uniqid()))
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($asserter->afterMethodCall('bar')->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()))
			->if($mock->getMockController()->resetCalls())
			->and($mock->bar(uniqid()))
			->and($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo(uniqid()))
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($asserter->beforeMethodCall('bar')->withArguments($arg = 'toto')->call('foo'))
			->and($mock->foo(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls())))
			->and($mock->foo(uniqid()))
			->if($mock->bar($arg))
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
		;
	}

	public function testThrice()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
			->if($asserter->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 3'), $asserter->getCall()))
			->if($call = new php\call('foo', null, $mock))
			->and($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($secondArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 2 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArg)))
			->if($mock->foo($thirdArg = uniqid()))
			->then
				->object($asserter->thrice())->isIdenticalTo($asserter)
			->if($mock->foo($fourthArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 4 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($thirdArg)) . PHP_EOL . '[4] ' . $call->setArguments(array($fourthArg)))
			->if($mock->getMockController()->resetCalls())
			->and($asserter->withArguments($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 3'), $asserter->getCall()))
			->if($mock->foo($usedArg))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($usedArg))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 2 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($usedArg))
			->then
				->object($asserter->thrice())->isIdenticalTo($asserter)
			->if($mock->foo($usedArg))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 4 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[4] ' . $call->setArguments(array($usedArg)))
			->if($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[4] ' . $call->setArguments(array($usedArg)))
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($asserter->beforeMethodCall('bar')->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 3'), $asserter->getCall()))
			->if($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($usedArg))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 2 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo(uniqid()))
			->then
				->object($asserter->thrice())->isIdenticalTo($asserter)
			->if($mock->getMockController()->resetCalls())
			->and($mock->foo($firstArg = uniqid()))
			->and($mock->bar(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArg)))
			->if($mock->foo($secondArg = uniqid()))
			->and($mock->bar(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 2 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArg)))
			->if($mock->foo(uniqid()))
			->and($mock->bar(uniqid()))
			->then
				->object($asserter->thrice())->isIdenticalTo($asserter)
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($asserter->afterMethodCall('bar')->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 3'), $asserter->getCall()))
			->if($mock->getMockController()->resetCalls())
			->and($mock->bar(uniqid()))
			->and($mock->foo($firstArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArg)))
			->if($mock->bar(uniqid()))
			->and($mock->foo($secondArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 2 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArg)))
			->if($mock->foo(uniqid()))
			->then
				->object($asserter->thrice())->isIdenticalTo($asserter)
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($asserter->beforeMethodCall('bar')->withArguments($arg = 'toto')->call('foo'))
			->and($mock->foo(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls())))
			->if($mock->foo(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls())))
			->and($mock->foo(uniqid()))
			->if($mock->bar($arg))
			->then
				->object($asserter->thrice())->isIdenticalTo($asserter)
		;
	}

	public function testAtLeastOnce()
	{

		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
			->if($asserter->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time'), $asserter->getCall()))
			->if($mock->foo(uniqid()))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->if($mock->foo(uniqid()))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->if($mock->getMockController()->resetCalls())
			->and($asserter->withArguments($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time'), $asserter->getCall()))
			->if($call = new php\call('foo', null, $mock))
			->if( $mock->foo($usedArg))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->if($asserter->withArguments($otherArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
		;
	}

	public function testExactly()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
			->if($asserter->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()))
			->if($call = new php\call('foo', null, $mock))
			->and($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($otherUsedArg = uniqid()))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->if($mock->foo($anOtherUsedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherUsedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($anOtherUsedArg)))
			->if($mock->getMockController()->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()))
			->if($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($arg))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
			->if($mock->foo($arg))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->if($mock->foo($arg))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)) . PHP_EOL . '[3] ' . $call->setArguments(array($arg)) . PHP_EOL . '[4] ' . $call->setArguments(array($arg)))
			->if($call = new php\call('fooWithSeveralArguments', null, $mock))
			->and($asserter->call('fooWithSeveralArguments'))
			->then
				->object($asserter->exactly(0))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->exactly(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()))
			->if($mock->fooWithSeveralArguments(1, 2, 3, 4, 5))
				->exception(function() use ($asserter) { $asserter->exactly(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array(1, 2, 3, 4, 5)))
				->object($asserter->exactly(1))->isIdenticalTo($asserter)
				->object($asserter->withArguments(1, 2, 3, 4, 5)->exactly(1))->isIdenticalTo($asserter)
				->object($asserter->withAtLeastArguments(array(1 => 2, 3 => 4))->exactly(1))->isIdenticalTo($asserter)
				->object($asserter->withAtLeastArguments(array(1 => 2, 3 => rand(6, PHP_INT_MAX)))->exactly(0))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->withAtLeastArguments(array(1 => 2, 3 => 4))->exactly(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array(1, 2, 3, 4, 5)))
				->object($asserter->withIdenticalArguments(1, 2, 3, 4, 5)->exactly(1))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->withAtLeastIdenticalArguments(array(1 => '2', 3 => 4))->exactly(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array(1, 2, 3, 4, 5)))
		;
	}

	public function testNever()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
			->if($asserter->call('foo'))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
			->if($call = new php\call('foo', null, $mock))
			->and($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->getMockController()->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
			->if($mock->foo($arg))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
			->if($mock->foo($arg))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('method %s is called 2 times instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
			->if($mock->foo($arg))
			->then
				->exception(function() use ($asserter, & $message) { $asserter->never($message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
			->if($asserter->withArguments(uniqid()))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
		;
	}
}
