<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters\adapter as sut
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
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getCall())->isEqualTo(new test\adapter\call())
				->variable($asserter->getAdapter())->isNull()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut(new asserter\generator()))
			->then
				->variable($asserter->getAdapter())->isNull()
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getAdapter())->isNull()
			->if($asserter->setWith($adapter = new atoum\test\adapter()))
			->then
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->sizeOf($adapter->getCalls())->isZero()
				->object($asserter->reset())->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->sizeOf($adapter->getCalls())->isZero()
			->if($adapter->md5(uniqid()))
			->then
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->sizeOf($adapter->getCalls())->isEqualTo(1)
				->object($asserter->reset())->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->sizeOf($adapter->getCalls())->isZero()
		;
	}

	public function testCall()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->call(uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function))
			->if($asserter->withArguments())
			->then
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array()))
				->object($asserter->disableEvaluationChecking()->call($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function))
		;
	}

	public function testWithArguments()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->withArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array()))
				->object($asserter->withArguments($arg1 = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array($arg1)))
				->object($asserter->disableEvaluationChecking()->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array($arg1, $arg2)))
		;
	}

	public function testWithAnyArguments()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function))
			->if($asserter->disableEvaluationChecking()->withArguments($arg = uniqid()))
			->then
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array($arg)))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function))
		;
	}

	public function testWithoutAnyArgument()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call($function = uniqid()))
			->then
				->object($asserter->disableEvaluationChecking()->withoutAnyArgument())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array()))
		;
	}

	public function testOnce()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call('md5'))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 1'), $asserter->getCall()))
			->if($call = new test\adapter\call('md5'))
			->and($adapter->md5($firstArgument = uniqid()))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($adapter->md5($secondArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->and($adapter->md5($arg))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
		;
	}

	public function testTwice()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call('md5'))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()))
			->if($call = new test\adapter\call('md5'))
			->and($adapter->md5($firstArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)))
			->if($adapter->md5($secondArgument = uniqid()))
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
			->if($adapter->md5($thirdArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)) . PHP_EOL . '[3] ' . $call->setArguments(array($thirdArgument)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->and($adapter->md5($arg))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					 ->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
		;
	}

	public function testThrice()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call('md5'))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 3'), $asserter->getCall()))
			->if($call = new test\adapter\call('md5'))
			->and($adapter->md5($firstArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)))
			->if($adapter->md5($secondArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)))
			->if($adapter->md5($thirdArgument = uniqid()))
			->then
				->object($asserter->thrice())->isIdenticalTo($asserter)
			->if($adapter->md5($fourthArgument = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 4 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)) . PHP_EOL . '[3] ' . $call->setArguments(array($thirdArgument)) . PHP_EOL . '[4] ' . $call->setArguments(array($fourthArgument)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->and($adapter->md5($arg))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
		;
	}

	public function testAtLeastOnce()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call('md5'))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time'), $asserter->getCall()))
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
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time'), $asserter->getCall()))
			->if($call = new test\adapter\call('md5'))
			->and($adapter->md5($arg))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
		;
	}

	public function testExactly()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($asserter->call('md5'))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()))
			->if($call = new test\adapter\call('md5'))
			->and($adapter->md5($arg = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
			->if($adapter->md5($otherArg = uniqid()))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->if($adapter->md5($anOtherArg = uniqid()))
			->then
				->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($anOtherArg)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()))
			->if($adapter->md5($usedArg = uniqid()))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
			->if($adapter->md5($arg))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $anAnotherLine, $asserter) { $anAnotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)) . PHP_EOL . '[3] ' . $call->setArguments(array($arg))  . PHP_EOL . '[4] ' . $call->setArguments(array($arg)))
		;
	}

	public function testNever()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
			->if($call = new test\adapter\call('md5'))
			->and($asserter->call('md5'))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
			->if($adapter->md5($usedArg = uniqid()))
			->then
					->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->if($adapter->resetCalls())
			->and($asserter->withArguments($arg = uniqid()))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
			->if($adapter->md5($arg))
			->then
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
			->if($asserter->withArguments(uniqid()))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
		;
	}

	public function testBefore()
	{
		$this
			->if($asserter = new sut($generator = new atoum\asserter\generator()))
			->and($adapter = new test\adapter())
			->and($adapter->shouldBeCallBefore = uniqid())
			->and($asserter->setWith($adapter))
			->and($beforeAsserter = new sut(new atoum\asserter\generator()))
			->and($beforeAdapter = new test\adapter())
			->and($beforeAdapter->wasCalledAfter = uniqid())
			->and($beforeAsserter->setWith($beforeAdapter))
			->and($asserter->call('shouldBeCallBefore')->before($beforeAsserter->call('wasCalledAfter')))
			->then
				->if($adapter->shouldBeCallBefore())
				->and($beforeAdapter->wasCalledAfter())
				->then
					->object($asserter->once())->isIdenticalTo($asserter)
			->if($adapter->resetCalls())
			->and($beforeAdapter->resetCalls())
			->and($beforeAdapter->wasCalledAfter())
			->and($adapter->shouldBeCallBefore())
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not called before %s'), $asserter->getCall(), $beforeAsserter->getCall()))
			->if($adapter->resetCalls())
			->and($beforeAdapter->resetCalls())
			->and($beforeAdapter->wasCalledAfter())
			->and($beforeAdapter->wasCalledAfter())
			->and($adapter->shouldBeCallBefore())
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not called before %s'), $asserter->getCall(), $beforeAsserter->getCall()))
			->if($adapter->resetCalls())
			->and($beforeAdapter->resetCalls())
			->and($adapter->shouldBeCallBefore())
			->and($beforeAdapter->wasCalledAfter())
			->and($beforeAdapter->wasCalledAfter())
			->and($adapter->shouldBeCallBefore())
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2 before %s'), $asserter->getCall(), $beforeAsserter->getCall()))
			->if($adapter->resetCalls())
			->and($beforeAdapter->resetCalls())
			->and($adapter->shouldBeCallBefore())
			->and($beforeAdapter->wasCalledAfter())
			->and($adapter->shouldBeCallBefore())
			->and($beforeAdapter->wasCalledAfter())
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
			->if($adapter->resetCalls())
			->and($beforeAdapter->resetCalls())
			->and($adapter->shouldBeCallBefore())
			->and($beforeAdapter->wasCalledAfter())
			->and($beforeAdapter->wasCalledAfter())
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($adapter->resetCalls())
			->and($beforeAdapter->resetCalls())
			->and($adapter->shouldBeCallBefore())
			->and($adapter->shouldBeCallBefore())
			->and($beforeAdapter->wasCalledAfter())
			->and($beforeAdapter->wasCalledAfter())
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
		;
	}

	public function testAfter()
	{
		$this
			->if($asserter = new sut($generator = new atoum\asserter\generator()))
			->and($adapter = new test\adapter())
			->and($adapter->shouldBeCallafter = uniqid())
			->and($asserter->setWith($adapter))
			->and($afterAsserter = new sut(new atoum\asserter\generator()))
			->and($afterAdapter = new test\adapter())
			->and($afterAdapter->wasCalledBefore = uniqid())
			->and($afterAsserter->setWith($afterAdapter))
			->and($asserter->call('shouldBeCallAfter')->after($afterAsserter->call('wasCalledBefore')))
			->and($afterAdapter->wasCalledBefore())
			->and($adapter->shouldBeCallAfter())
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($adapter->resetCalls())
			->and($afterAdapter->resetCalls())
			->and($adapter->shouldBeCallAfter())
			->and($afterAdapter->wasCalledBefore())
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not called after %s'), $asserter->getCall(), $afterAsserter->getCall()))
			->if($adapter->resetCalls())
			->and($afterAdapter->resetCalls())
			->and($adapter->shouldBeCallAfter())
			->and($adapter->shouldBeCallAfter())
			->and($afterAdapter->wasCalledBefore())
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not called after %s'), $asserter->getCall(), $afterAsserter->getCall()))
			->if($adapter->resetCalls())
			->and($afterAdapter->resetCalls())
			->and($adapter->shouldBeCallAfter())
			->and($afterAdapter->wasCalledBefore())
			->and($adapter->shouldBeCallAfter())
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2 after %s'), $asserter->getCall(), $afterAsserter->getCall()))
			->if($adapter->resetCalls())
			->and($afterAdapter->resetCalls())
			->and($afterAdapter->wasCalledBefore())
			->and($adapter->shouldBeCallAfter())
			->and($afterAdapter->wasCalledBefore())
			->and($adapter->shouldBeCallAfter())
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
			->if($adapter->resetCalls())
			->and($afterAdapter->resetCalls())
			->and($afterAdapter->wasCalledBefore())
			->and($adapter->shouldBeCallAfter())
			->and($afterAdapter->wasCalledBefore())
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($adapter->resetCalls())
			->and($afterAdapter->resetCalls())
			->and($afterAdapter->wasCalledBefore())
			->and($adapter->shouldBeCallAfter())
			->and($afterAdapter->wasCalledBefore())
			->and($adapter->shouldBeCallAfter())
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
			->if($adapter->resetCalls())
			->and($afterAdapter->resetCalls())
			->and($afterAdapter->wasCalledBefore())
			->and($adapter->shouldBeCallAfter())
			->and($adapter->shouldBeCallAfter())
			->and($afterAdapter->wasCalledBefore())
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
			->if($adapter->resetCalls())
			->and($afterAdapter->resetCalls())
			->and($afterAdapter->wasCalledBefore())
			->and($afterAdapter->wasCalledBefore())
			->and($adapter->shouldBeCallAfter())
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
		;
	}
}
