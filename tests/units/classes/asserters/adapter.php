<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\asserters\adapter as testedClass
;

require_once __DIR__ . '/../../runner.php';

class adapter extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getAdapter())->isNull()
				->exception(function() use ($asserter) { $asserter->getCallAsserter(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->assert('Set the asserter with something else than an adapter throw an exception')
					->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s is not a test adapter'), $asserter->getTypeOf($value)))
					->string($asserter->getAdapter())->isEqualTo($value)
				->assert('It is possible to set the asserter with an adapter')
					->object($asserter->setWith($adapter = new test\adapter()))->isIdenticalTo($asserter)
					->object($asserter->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testReset()
	{
		$this
			->if($asserter = new testedClass(new asserter\generator()))
			->then
				->variable($asserter->getAdapter())->isNull()
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getAdapter())->isNull()
			->if($asserter->setWith($adapter = new atoum\test\adapter()))
			->then
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->array($adapter->getCalls())->isEmpty()
				->object($asserter->reset())->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->array($adapter->getCalls())->isEmpty()
			->if($adapter->md5(uniqid()))
			->then
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->array($adapter->getCalls())->isNotEmpty()
				->object($asserter->reset())->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->array($adapter->getCalls())->isEmpty()
		;
	}

	public function testGetCallAsserter()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->exception(function() use ($asserter) { $asserter->getCallAsserter(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->object($asserter->getCallAsserter())->isEqualTo(new asserters\call\adapter($asserter))
		;
	}

	public function testCall()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->exception(function() use ($asserter) { $asserter->call(uniqid()); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter->getCallAsserter())
		;
	}

	public function testWithArguments()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($call = $asserter->call($function = uniqid()))
			->then
				->object($asserter->withArguments())->isIdenticalTo($call)
				->array($call->getCall()->getArguments())->isEmpty()
				->object($asserter->withArguments($arg1 = uniqid()))->isIdenticalTo($call)
				->array($call->getCall()->getArguments())->isEqualTo(array($arg1))
				->object($asserter->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($call)
				->array($call->getCall()->getArguments())->isEqualTo(array($arg1, $arg2))
		;
	}

	public function testWithAnyArguments()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($call = $asserter->call($function = uniqid()))
			->then
				->object($asserter->withAnyArguments())->isIdenticalTo($call)
				->variable($call->getCall()->getArguments())->isNull()
		;
	}

	public function testWithoutAnyArgument()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($call = $asserter->call($function = uniqid()))
			->then
				->object($asserter->withoutAnyArgument())->isIdenticalTo($call)
				->array($call->getCall()->getArguments())->isEmpty()
		;
	}

	public function testBeforeMethodCall()
	{
		$this
			->if($mock = new \mock\dummy())
			->and($asserter = new testedClass())
			->then
				->exception(function() use ($asserter, $mock) { $asserter->beforeMethodCall(uniqid(), $mock); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEmpty()
				->object($asserter->beforeMethodCall('foo', $mock))->isEqualTo($beforeMethodCall = new asserters\adapter\call\mock($asserter->getCallAsserter(), $mock, 'foo'))
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall))
				->object($asserter->beforeMethodCall('bar', $mock))->isEqualTo($otherBeforeMethodCall = new asserters\adapter\call\mock($asserter->getCallAsserter(), $mock, 'bar'))
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall, $otherBeforeMethodCall))
		;
	}

	public function testWithAnyMethodCallsBefore()
	{
		$this
			->if($mock = new \mock\dummy())
			->and($asserter = new asserters\adapter())
			->and($asserter->setWith($adapter = new test\adapter()))
			->then
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEmpty()
			->if($asserter->setWith($adapter = new test\adapter()))
			->and($asserter->beforeMethodCall(uniqid(), $mock))
			->then
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEmpty()
			->if($asserter->beforeMethodCall($method1 = uniqid(), $mock)->beforeMethodCall($method2 = uniqid(), $mock))
			->then
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getBeforeMethodCalls())->isEmpty()
		;
	}

	public function testAfterMethodCall()
	{
		$this
			->if($mock = new \mock\dummy())
			->and($asserter = new testedClass())
			->then
				->exception(function() use ($asserter, $mock) { $asserter->afterMethodCall(uniqid(), $mock); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
				->if($asserter->setWith($adapter = new test\adapter()))
				->then
					->object($asserter->afterMethodCall('foo', $mock))->isEqualTo($afterMethodCall = new asserters\adapter\call\mock($asserter->getCallAsserter(), $mock, 'foo'))
					->array($asserter->getCallAsserter()->getAfterMethodCalls())->isEqualTo(array($afterMethodCall))
					->object($asserter->afterMethodCall('bar', $mock))->isEqualTo($otherAfterMethodCall = new asserters\adapter\call\mock($asserter->getCallAsserter(), $mock, 'bar'))
					->array($asserter->getCallAsserter()->getAfterMethodCalls())->isEqualTo(array($afterMethodCall, $otherAfterMethodCall))
		;
	}

	public function testWithAnyMethodCallsAfter()
	{
		$this
			->if($mock = new \mock\dummy())
			->and($asserter = new testedClass(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withAnyMethodCallsAfter(); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->and($asserter->afterMethodCall(uniqid(), $mock))
			->then
				->array($asserter->getCallAsserter()->getAfterMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getAfterMethodCalls())->isEmpty()
			->if($asserter
				->afterMethodCall($method1 = uniqid(), $mock)
				->afterMethodCall($method2 = uniqid(), $mock)
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
			->if($mock = new \mock\dummy())
			->and($asserter = new testedClass(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->beforeFunctionCall(uniqid(), new test\adapter()); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->object($asserter->beforeFunctionCall('foo'))->isEqualTo($beforeFunctionCall = new asserters\adapter\call\adapter($asserter->getCallAsserter(), $adapter, 'foo'))
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall))
				->object($asserter->beforeFunctionCall('bar'))->isEqualTo($otherBeforeFunctionCall = new asserters\adapter\call\adapter($asserter->getCallAsserter(), $adapter, 'bar'))
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall, $otherBeforeFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsBefore()
	{
		$this
			->if($asserter = new testedClass(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->beforeFunctionCall(uniqid(), new test\adapter()); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isEmpty()
			->if($asserter->beforeFunctionCall(uniqid()))
			->then
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getBeforeFunctionCalls())->isEmpty()
			->if($asserter
				->beforeFunctionCall($method1 = uniqid())
				->beforeFunctionCall($method2 = uniqid())
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
			->if($mock = new \mock\dummy())
			->and($asserter = new testedClass(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->afterFunctionCall(uniqid(), new test\adapter()); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->object($asserter->afterFunctionCall('foo'))->isEqualTo($afterFunctionCall = new asserters\adapter\call\adapter($asserter->getCallAsserter(), $adapter, 'foo'))
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall))
				->object($asserter->afterFunctionCall('bar'))->isEqualTo($otherAfterFunctionCall = new asserters\adapter\call\adapter($asserter->getCallAsserter(), $adapter, 'bar'))
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall, $otherAfterFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsAfter()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->exception(function() use ($asserter) { $asserter->afterFunctionCall(uniqid(), new test\adapter()); })
					->isInstanceOf('atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEmpty()
			->if($asserter->afterFunctionCall(uniqid()))
			->then
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEmpty()
			->if($asserter->afterFunctionCall($method1 = uniqid())->afterFunctionCall($method2 = uniqid()))
			->then
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter->getCallAsserter())
				->array($asserter->getCallAsserter()->getAfterFunctionCalls())->isEmpty()
		;
	}
}
