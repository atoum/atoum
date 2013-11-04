<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\test\adapter\call\decorators,
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
				->variable($asserter->getAdapter())->isNull()
				->variable($asserter->getCall())->isEqualTo(new test\adapter\call())
		;
	}

	public function testReset()
	{
		$this
			->if($mockController = new \mock\mageekguy\atoum\mock\controller())
			->and($asserter = new sut($generator = new asserter\generator()))
			->then
				->variable($asserter->getAdapter())->isNull()
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getAdapter())->isNull()
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\score()))
			->and($mock->setMockController($mockController))
			->and($this->resetMock($mockController))
			->then
				->object($asserter->getAdapter())->isIdenticalTo($mock->getMockController())
				->object($asserter->reset())->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isIdenticalTo($mock->getMockController())
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
				->object($asserter->getAdapter())->isIdenticalTo($mock->getMockController())
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

	public function testCall()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->call(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, null, new decorators\addClass($mock->getMockController()->getMockClass())))
			->if($asserter->withArguments())
			->then
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array(), new decorators\addClass($mock->getMockController()->getMockClass())))
				->object($asserter->disableEvaluationChecking()->call($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, null, new decorators\addClass($mock->getMockController()->getMockClass())))
		;
	}

	public function testWithArguments()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->withArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array(), new decorators\addClass($mock)))
				->object($asserter->withArguments($arg1 = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array($arg1),  new decorators\addClass($mock)))
				->object($asserter->disableEvaluationChecking()->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array($arg1, $arg2),  new decorators\addClass($mock)))
		;
	}

	public function testWithAtLeastArguments()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->withAtLeastArguments($arguments = array(1 => uniqid())))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, $arguments, new decorators\addClass($mock)))
				->object($asserter->disableEvaluationChecking()->withAtLeastArguments($arguments = array(2 => uniqid(), 5 => uniqid())))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, $arguments, new decorators\addClass($mock)))
		;
	}

	public function testWithAnyArguments()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, null, new decorators\addClass($mock)))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, null, new decorators\addClass($mock)))
			->if($asserter->disableEvaluationChecking()->withArguments($arg = uniqid()))
			->then
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array($arg), new decorators\addClass($mock)))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, null, new decorators\addClass($mock)))
		;
	}

	public function testWithoutAnyArgument()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
			->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->disableEvaluationChecking()->withoutAnyArgument())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array(), new decorators\addClass($mock)))
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
					->hasMessage('Call is undefined')
			->if($asserter->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 1'), $asserter->getCall()))
			->if($call = new test\adapter\call('foo', null, new decorators\addClass($mock->getMockController()->getMockClass())))
			->and($call->setDecorator(new decorators\addClass($mock)))
			->and($mock->foo($usedArg = uniqid()))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($mock->foo($otherUsedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherUsedArg)))
			->if($mock->getMockController()->resetCalls())
			->and($asserter->withArguments($usedArg = uniqid()))
			->and($mock->foo($usedArg))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
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
					->hasMessage('Call is undefined')
			->if($asserter->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()))
			->if($call = new test\adapter\call('foo', null, new decorators\addClass($mock)))
			->and($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($secondArg = uniqid()))
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
			->if($mock->foo($thirdArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($thirdArg)))
			->if($mock->getMockController()->resetCalls())
			->and($asserter->withArguments($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()))
			->if($mock->foo($usedArg))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($usedArg))
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
			->if($mock->foo($usedArg))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($usedArg)))
			->if($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($usedArg)))
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
					->hasMessage('Call is undefined')
			->if($asserter->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 3'), $asserter->getCall()))
			->if($call = new test\adapter\call('foo', null, new decorators\addClass($mock)))
			->and($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($secondArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArg)))
			->if($mock->foo($thirdArg = uniqid()))
			->then
				->object($asserter->thrice())->isIdenticalTo($asserter)
			->if($mock->foo($fourthArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 4 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($thirdArg)) . PHP_EOL . '[4] ' . $call->setArguments(array($fourthArg)))
			->if($mock->getMockController()->resetCalls())
			->and($asserter->withArguments($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 3'), $asserter->getCall()))
			->if($mock->foo($usedArg))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($usedArg))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($usedArg))
			->then
				->object($asserter->thrice())->isIdenticalTo($asserter)
			->if($mock->foo($usedArg))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 4 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[4] ' . $call->setArguments(array($usedArg)))
			->if($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[4] ' . $call->setArguments(array($usedArg)))
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
					->hasMessage('Call is undefined')
			->if($asserter->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time'), $asserter->getCall()))
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
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time'), $asserter->getCall()))
			->if($call = new test\adapter\call('foo', null,  new decorators\addClass($mock)))
			->if( $mock->foo($usedArg))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->if($asserter->withArguments($otherArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
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
					->hasMessage('Call is undefined')
			->if($asserter->call('foo'))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()))
			->if($call = new test\adapter\call('foo', null, new decorators\addClass($mock)))
			->and($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($otherUsedArg = uniqid()))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->if($mock->foo($anOtherUsedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherUsedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($anOtherUsedArg)))
			->if($mock->getMockController()->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()))
			->if($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->foo($arg))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
			->if($mock->foo($arg))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->if($mock->foo($arg))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)) . PHP_EOL . '[3] ' . $call->setArguments(array($arg)) . PHP_EOL . '[4] ' . $call->setArguments(array($arg)))
			->if($call = new test\adapter\call('fooWithSeveralArguments', null,  new decorators\addClass($mock)))
			->and($asserter->call('fooWithSeveralArguments'))
			->then
				->object($asserter->exactly(0))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->exactly(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 1'), $asserter->getCall()))
			->if($mock->fooWithSeveralArguments(1, 2, 3, 4, 5))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array(1, 2, 3, 4, 5)))
				->object($asserter->exactly(1))->isIdenticalTo($asserter)
				->object($asserter->withArguments(1, 2, 3, 4, 5)->exactly(1))->isIdenticalTo($asserter)
				->object($asserter->withAtLeastArguments(array(1 => 2, 3 => 4))->exactly(1))->isIdenticalTo($asserter)
				->object($asserter->withAtLeastArguments(array(1 => 2, 3 => rand(6, PHP_INT_MAX)))->exactly(0))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->withAtLeastArguments(array(1 => 2, 3 => 4))->exactly(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array(1, 2, 3, 4, 5)))
				->object($asserter->withIdenticalArguments(1, 2, 3, 4, 5)->exactly(1))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->withAtLeastIdenticalArguments(array(1 => '2', 3 => 4))->exactly(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array(1, 2, 3, 4, 5)))
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
					->hasMessage('Call is undefined')
			->if($asserter->call('foo'))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
			->if($call = new test\adapter\call('foo', null, new decorators\addClass($mock)))
			->and($mock->foo($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($mock->getMockController()->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
			->if($mock->foo($arg))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
			->if($mock->foo($arg))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
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
