<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\asserters\mock as testedClass;
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
			->if($asserter = new asserters\mock($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getMock())->isNull()
				->exception(function() use ($asserter) { $asserter->getCallAsserter(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;
	}

	public function testReset()
	{
		$this
			->if($mockController = new \mock\mageekguy\atoum\mock\controller())
			->and($asserter = new asserters\mock($generator = new asserter\generator()))
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
			->if($asserter = new asserters\mock($generator = new asserter\generator()))
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
			->if($asserter = new asserters\mock($generator = new asserter\generator()))
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
			->if($asserter = new asserters\mock($generator = new asserter\generator()))
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
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->beforeMethodCall(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->object($asserter->beforeMethodCall('foo'))->isEqualTo($beforeMethodCall = new asserters\mock\call\mock($asserter->getCallAsserter(), $mock, 'foo'))
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall))
				->object($asserter->beforeMethodCall('bar'))->isEqualTo($otherBeforeMethodCall = new asserters\mock\call\mock($asserter->getCallAsserter(), $mock, 'bar'))
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall, $otherBeforeMethodCall))
		;
	}

	public function testWithAnyMethodCallsBefore()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->beforeMethodCall(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith(new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEmpty()
			->if($asserter->beforeMethodCall(uniqid()))
			->then
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEmpty()
			->if($asserter
				->beforeMethodCall(uniqid())
				->beforeMethodCall(uniqid())
			)
			->then
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEmpty()
		;
	}

	public function testAfterMethodCall()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->afterMethodCall(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->object($asserter->afterMethodCall('foo'))->isEqualTo($afterMethodCall = new asserters\mock\call\mock($asserter->getCallAsserter(), $mock, 'foo'))
				->array($asserter->getCallAsserter()->getAfterMethodCalls())->isEqualTo(array($afterMethodCall))
				->object($asserter->afterMethodCall('bar'))->isEqualTo($otherAfterMethodCall = new asserters\mock\call\mock($asserter->getCallAsserter(), $mock, 'bar'))
				->array($asserter->getCallAsserter()->getAfterMethodCalls())->isEqualTo(array($afterMethodCall, $otherAfterMethodCall))
		;
	}

	public function testWithAnyMethodCallsAfter()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withAnyMethodCallsAfter(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->array($asserter->getCallAsserter()->getAfterMethodCalls())->isEmpty()
				->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getAfterMethodCalls())->isEmpty()
			->if($asserter->afterMethodCall($function = uniqid()))
			->then
				->array($asserter->getCallAsserter()->getAfterMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getAfterMethodCalls())->isEmpty()
			->if($asserter
				->afterMethodCall($function1 = uniqid())
				->afterMethodCall($function2 = uniqid())
			)
			->then
				->array($asserter->getCallAsserter()->getAfterMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getAfterMethodCalls())->isEmpty()
		;
	}

	public function testBeforeFunctionCall()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->beforeFunctionCall(uniqid(), new test\adapter()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($adapter = new test\adapter())
			->then
				->object($asserter->beforeFunctionCall('foo', $adapter))->isEqualTo($beforeFunctionCall = new asserters\mock\call\adapter($asserter->getCallAsserter(), $adapter, 'foo'))
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall))
				->object($asserter->beforeFunctionCall('bar', $adapter))->isEqualTo($otherBeforeFunctionCall = new asserters\mock\call\adapter($asserter->getCallAsserter(), $adapter, 'bar'))
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall, $otherBeforeFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsBefore()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withAnyMethodCallsAfter(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isEmpty()
				->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isEmpty()
			->if($adapter = new test\adapter())
			->and($asserter->beforeFunctionCall($function = uniqid(), $adapter))
			->then
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isEmpty()
			->if($asserter
				->beforeFunctionCall($function1 = uniqid(), $adapter)
				->beforeFunctionCall($function2 = uniqid(), $adapter)
			)
			->then
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isEmpty()
		;
	}

	public function testAfterFunctionCall()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->afterFunctionCall(uniqid(), new test\adapter()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->and($adapter = new test\adapter())
			->then
				->object($asserter->afterFunctionCall('foo', $adapter))->isEqualTo($afterFunctionCall = new asserters\mock\call\adapter($asserter->getCallAsserter(), $adapter, 'foo'))
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall))
				->object($asserter->afterFunctionCall('bar', $adapter))->isEqualTo($otherAfterFunctionCall = new asserters\mock\call\adapter($asserter->getCallAsserter(), $adapter, 'bar'))
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall, $otherAfterFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsAfter()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withAnyFunctionCallsAfter(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEmpty()
			->if($adapter = new test\adapter())
			->and($asserter->afterFunctionCall($function = uniqid(), $adapter))
			->then
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEmpty()
			->if($asserter
				->afterFunctionCall($function1 = uniqid(), $adapter)
				->afterFunctionCall($function2 = uniqid(), $adapter)
			)
			->then
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEmpty()
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
				->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter->getCallAsserter())
		;
	}

	public function testWithArguments()
	{
		$this
			->if($asserter = new testedClass(new asserter\generator()))
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
				->object($asserter->withArguments())->isIdenticalTo($asserter->getCallAsserter())
				->object($asserter->getCallAsserter()->getCall())->isEqualTo(new php\call($function, array(), $mock))
				->object($asserter->withArguments($arg1 = uniqid()))->isIdenticalTo($asserter->getCallAsserter())
				->object($asserter->getCallAsserter()->getCall())->isEqualTo(new php\call($function, array($arg1), $mock))
				->object($asserter->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($asserter->getCallAsserter())
				->object($asserter->getCallAsserter()->getCall())->isEqualTo(new php\call($function, array($arg1, $arg2), $mock))
		;
	}

	public function testWithAtLeastArguments()
	{
		$this
			->if($asserter = new testedClass(new asserter\generator()))
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
				->object($asserter->withAtLeastArguments($arguments = array(1 => uniqid())))->isIdenticalTo($asserter->getCallAsserter())
				->object($asserter->getCallAsserter()->getCall())->isEqualTo(new php\call($function, $arguments, $mock))
				->object($asserter->withAtLeastArguments($arguments = array(2 => uniqid(), 5 => uniqid())))->isIdenticalTo($asserter->getCallAsserter())
				->object($asserter->getCallAsserter()->getCall())->isEqualTo(new php\call($function, $arguments, $mock))
		;
	}

	public function testWithAnyArguments()
	{
		$this
			->if($asserter = new testedClass(new asserter\generator()))
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
				->object($asserter->getCallAsserter()->getCall())->isEqualTo(new php\call($function, null, $mock))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter->getCallAsserter())
				->object($asserter->getCallAsserter()->getCall())->isEqualTo(new php\call($function, null, $mock))
			->if($asserter->withArguments($arg = uniqid()))
			->then
				->object($asserter->getCallAsserter()->getCall())->isEqualTo(new php\call($function, array($arg), $mock))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter->getCallAsserter())
				->object($asserter->getCallAsserter()->getCall())->isEqualTo(new php\call($function, null, $mock))
		;
	}

	public function testWithoutAnyArgument()
	{
		$this
			->if($asserter = new testedClass(new asserter\generator()))
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
				->object($asserter->withoutAnyArgument())->isIdenticalTo($asserter->getCallAsserter())
				->object($asserter->getCallAsserter()->getCall())->isEqualTo(new php\call($function, array(), $mock))
		;
	}
}
