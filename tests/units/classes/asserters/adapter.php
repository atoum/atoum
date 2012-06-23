<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
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
			->if($asserter = new asserters\adapter($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getCall())->isNull()
				->variable($asserter->getAdapter())->isNull()
				->array($asserter->getBeforeMethodCalls())->isEmpty()
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
				->array($asserter->getAfterMethodCalls())->isEmpty()
				->array($asserter->getAfterFunctionCalls())->isEmpty()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\adapter($generator = new asserter\generator()))
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
			->if($asserter = new asserters\adapter(new asserter\generator()))
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

	public function testCall()
	{
		$this
			->if($asserter = new \mock\mageekguy\atoum\asserters\adapter(new asserter\generator()))
			->and($asserter->getMockController()->atLeastOnce = function() {})
			->then
				->exception(function() use ($asserter) { $asserter->call(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function))
			->if($asserter->withArguments())
			->then
				->object($asserter->getCall())->isEqualTo(new php\call($function, array()))
				->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function))
		;
	}

	public function testWithArguments()
	{
		$this
			->if($asserter = new \mock\mageekguy\atoum\asserters\adapter(new asserter\generator()))
			->and($asserter->getMockController()->atLeastOnce = function() {})
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->withArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, array()))
				->object($asserter->withArguments($arg1 = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg1)))
				->object($asserter->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg1, $arg2)))
		;
	}

	public function testWithAnyArguments()
	{
		$this
			->if($asserter = new \mock\mageekguy\atoum\asserters\adapter(new asserter\generator()))
			->and($asserter->getMockController()->atLeastOnce = function() {})
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->getCall())->isEqualTo(new php\call($function))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function))
			->if($asserter->withArguments($arg = uniqid()))
			->then
				->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg)))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function))
		;
	}

	public function testBeforeMethodCall()
	{
		$this
			->if($mock = new \mock\dummy())
			->and($asserter = new asserters\adapter(new asserter\generator()))
			->then
				->exception(function() use ($asserter, $mock) { $asserter->beforeMethodCall(uniqid(), $mock); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
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
			->if($asserter = new asserters\adapter(new asserter\generator()))
			->then
				->array($asserter->getBeforeMethodCalls())->isEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeMethodCalls())->isEmpty()
			->if($asserter->setWith($adapter = new test\adapter()))
			->and($asserter->beforeMethodCall(uniqid(), new \mock\dummy()))
			->then
				->array($asserter->getBeforeMethodCalls())->isNotEmpty()
				->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeMethodCalls())->isEmpty()
			->if($asserter->beforeMethodCall($method1 = uniqid(), new \mock\dummy())->beforeMethodCall($method2 = uniqid(), new \mock\dummy()))
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
			->and($asserter = new asserters\adapter(new asserter\generator()))
			->then
				->exception(function() use ($asserter, $mock) { $asserter->afterMethodCall(uniqid(), $mock); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
				->if($asserter->setWith($adapter = new test\adapter()))
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
			->if($asserter = new asserters\adapter(new asserter\generator()))
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
			->and($asserter = new asserters\adapter(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->beforeFunctionCall(uniqid(), new test\adapter()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
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
			->if($asserter = new asserters\adapter(new asserter\generator()))
			->then
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
				->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
			->if($asserter->setWith($adapter = new test\adapter()))
			->and($asserter->beforeFunctionCall(uniqid()))
			->then
				->array($asserter->getBeforeFunctionCalls())->isNotEmpty()
				->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
				->array($asserter->getBeforeFunctionCalls())->isEmpty()
			->if($asserter
				->beforeFunctionCall($method1 = uniqid())
				->beforeFunctionCall($method2 = uniqid())
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
			->and($asserter = new asserters\adapter(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->afterFunctionCall(uniqid(), new test\adapter()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->object($asserter->afterFunctionCall('foo'))->isEqualTo($afterFunctionCall = new asserters\adapter\call\adapter($asserter, $adapter, 'foo'))
				->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall))
				->object($asserter->afterFunctionCall('bar'))->isEqualTo($otherAfterFunctionCall = new asserters\adapter\call\adapter($asserter, $adapter, 'bar'))
				->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall, $otherAfterFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsAfter()
	{
		$this
			->if($asserter = new asserters\adapter(new asserter\generator()))
			->then
				->array($asserter->getAfterFunctionCalls())->isEmpty()
				->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
				->array($asserter->getAfterFunctionCalls())->isEmpty()
			->if($asserter->setWith($adapter = new test\adapter()))
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

	public function testOnce()
	{
		$this
			->if($asserter = new asserters\adapter($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->call('md5'))
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 1'), $asserter->getCall()))
			->if($call = new php\call('md5'))
			->and($adapter->md5($firstArgument = uniqid()))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($adapter->md5($secondArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->and($adapter->md5($arg))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
		;
	}

	public function testAtLeastOnce()
	{
		$this
			->if($asserter = new asserters\adapter($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->call('md5'))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time'), $asserter->getCall()))
			->if($adapter->md5(uniqid()))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->if($adapter->md5(uniqid()))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time'), $asserter->getCall()))
			->if($call = new php\call('md5'))
			->and($adapter->md5($arg))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
		;
	}

	public function testExactly()
	{
		$this
			->if($asserter = new asserters\adapter($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->call('md5'))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()))
			->if($call = new php\call('md5'))
			->and($adapter->md5($arg = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
			->if($adapter->md5($otherArg = uniqid()))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->if($adapter->md5($anOtherArg = uniqid()))
			->then
				->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($anOtherArg)))
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
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
			->if($adapter->md5($arg))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $anAnotherLine, $asserter) { $anAnotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)) . PHP_EOL . '[3] ' . $call->setArguments(array($arg))  . PHP_EOL . '[4] ' . $call->setArguments(array($arg)))
		;
	}

	public function testNever()
	{
		$this
			->if($asserter = new asserters\adapter($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($call = new php\call('md5'))
			->and($asserter->call('md5'))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
			->if($adapter->md5($usedArg = uniqid()))
			->then
					->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('function %s is called 2 times instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
			->if($asserter->withArguments(uniqid()))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
		;
	}
}
