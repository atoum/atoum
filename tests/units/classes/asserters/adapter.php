<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter
;

require_once __DIR__ . '/../../runner.php';

class adapter extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->given($this->newTestedInstance($generator = new asserter\generator()))
			->then
				->object($this->testedInstance->getLocale())->isIdenticalTo($generator->getLocale())
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->variable($this->testedInstance->getCall())->isEqualTo(new test\adapter\call())
				->variable($this->testedInstance->getAdapter())->isNull()
		;
	}

	public function testSetWith()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator())
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)

			->if(
				$this->calling($locale)->_ = $notAnAdapter = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notAnAdapter)
				->mock($locale)->call('_')->withArguments('%s is not a test adapter', $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
				->string($asserter->getAdapter())->isEqualTo($value)

				->object($asserter->setWith($adapter = new test\adapter()))->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testReset()
	{
		$this
			->if($this->newTestedInstance(new asserter\generator()))
			->then
				->variable($this->testedInstance->getAdapter())->isNull()
				->object($this->testedInstance->reset())->isIdenticalTo($this->testedInstance)
				->variable($this->testedInstance->getAdapter())->isNull()

			->if($this->testedInstance->setWith($adapter = new atoum\test\adapter()))
			->then
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
				->sizeOf($adapter->getCalls())->isZero()
				->object($this->testedInstance->reset())->isIdenticalTo($this->testedInstance)
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
				->sizeOf($adapter->getCalls())->isZero()

			->if($adapter->md5(uniqid()))
			->then
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
				->sizeOf($adapter->getCalls())->isEqualTo(1)
				->object($this->testedInstance->reset())->isIdenticalTo($this->testedInstance)
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
				->sizeOf($adapter->getCalls())->isZero()
		;
	}

	public function testCall()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = $this->newTestedInstance(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->call(uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($this->testedInstance->setWith($adapter = new test\adapter()))
			->then
				->object($this->testedInstance->call($function = uniqid()))->isIdenticalTo($this->testedInstance)
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))

			->if($this->testedInstance->withArguments())
			->then
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, array()))
				->object($this->testedInstance->disableEvaluationChecking()->call($function = uniqid()))->isIdenticalTo($this->testedInstance)
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))
		;
	}

	public function testWithArguments()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = $this->newTestedInstance(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if($this->testedInstance->call($function = uniqid()))
			->then
				->object($this->testedInstance->withArguments())->isIdenticalTo($this->testedInstance)
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, array()))
				->object($this->testedInstance->withArguments($arg1 = uniqid()))->isIdenticalTo($this->testedInstance)
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, array($arg1)))
				->object($this->testedInstance->disableEvaluationChecking()->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($this->testedInstance)
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, array($arg1, $arg2)))
		;
	}

	public function testWithAnyArguments()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = $this->newTestedInstance(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if($this->testedInstance->call($function = uniqid()))
			->then
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))
				->object($this->testedInstance->withAnyArguments())->isIdenticalTo($this->testedInstance)
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))

			->if($this->testedInstance->disableEvaluationChecking()->withArguments($arg = uniqid()))
			->then
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, array($arg)))
				->object($this->testedInstance->withAnyArguments())->isIdenticalTo($this->testedInstance)
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))
		;
	}

	public function testWithoutAnyArgument()
	{
		$this
			->mockGenerator->orphanize('asserterFail')
			->if($asserter = $this->newTestedInstance(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if($this->testedInstance->call($function = uniqid()))
			->then
				->object($this->testedInstance->disableEvaluationChecking()->withoutAnyArgument())->isIdenticalTo($this->testedInstance)
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, array()))
		;
	}

	public function testOnce()
	{
		$this
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if($asserter->call('md5'))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 1'), $asserter->getCall()))
				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 1'), $asserter->getCall()))

			->if(
				$call = new test\adapter\call('md5'),
				$adapter->md5($firstArgument = uniqid())
			)
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
				->object($asserter->once)->isIdenticalTo($asserter)

			->if($adapter->md5($secondArgument = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)))
				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)))

			->if(
				$adapter->resetCalls(),
				$asserter->withArguments($arg = uniqid()),
				$adapter->md5($arg)
			)
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
				->object($asserter->once)->isIdenticalTo($asserter)

			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
				->exception(function() use ($asserter, & $failMessage) { $asserter->once($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testTwice()
	{
		$this
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if($asserter->call('md5'))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()))
				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()))

			->if(
				$call = new test\adapter\call('md5'),
				$adapter->md5($firstArgument = uniqid())
			)
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)))
				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)))

			->if($adapter->md5($secondArgument = uniqid()))
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
				->object($asserter->twice)->isIdenticalTo($asserter)

			->if($adapter->md5($thirdArgument = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)) . PHP_EOL . '[3] ' . $call->setArguments(array($thirdArgument)))
				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)) . PHP_EOL . '[3] ' . $call->setArguments(array($thirdArgument)))

			->if(
				$adapter->resetCalls(),
				$asserter->withArguments($arg = uniqid()),
				$adapter->md5($arg)
			)
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					 ->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					 ->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))

			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
				->exception(function() use ($asserter, & $failMessage) { $asserter->twice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testThrice()
	{
		$this
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if($asserter->call('md5'))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 3'), $asserter->getCall()))
				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 3'), $asserter->getCall()))

			->if(
				$call = new test\adapter\call('md5'),
				$adapter->md5($firstArgument = uniqid())
			)
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)))
				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)))

			->if($adapter->md5($secondArgument = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)))
				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)))

			->if($adapter->md5($thirdArgument = uniqid()))
			->then
				->object($asserter->thrice())->isIdenticalTo($asserter)
				->object($asserter->thrice)->isIdenticalTo($asserter)

			->if($adapter->md5($fourthArgument = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 4 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)) . PHP_EOL . '[3] ' . $call->setArguments(array($thirdArgument)) . PHP_EOL . '[4] ' . $call->setArguments(array($fourthArgument)))
				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 4 times instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)) . PHP_EOL . '[3] ' . $call->setArguments(array($thirdArgument)) . PHP_EOL . '[4] ' . $call->setArguments(array($fourthArgument)))

			->if(
				$adapter->resetCalls(),
				$asserter->withArguments($arg = uniqid()),
				$adapter->md5($arg)
			)
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))

			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 3'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
				->exception(function() use ($asserter, & $failMessage) { $asserter->thrice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testAtLeastOnce()
	{
		$this
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
				->exception(function() use ($asserter) { $asserter->atLeastOnce; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
				->exception(function() use ($asserter) { $asserter->atLeastOnce; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if($asserter->call('md5'))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time'), $asserter->getCall()))
				->exception(function() use ($asserter) { $asserter->atLeastOnce; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time'), $asserter->getCall()))

			->if($adapter->md5(uniqid()))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
				->object($asserter->atLeastOnce)->isIdenticalTo($asserter)

			->if($adapter->md5(uniqid()))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
				->object($asserter->atLeastOnce)->isIdenticalTo($asserter)

			->if(
				$adapter->resetCalls(),
				$asserter->withArguments($arg = uniqid())
			)
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time'), $asserter->getCall()))
				->exception(function() use ($asserter) { $asserter->atLeastOnce; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time'), $asserter->getCall()))

			->if(
				$call = new test\adapter\call('md5'),
				$adapter->md5($arg)
			)
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
				->object($asserter->atLeastOnce)->isIdenticalTo($asserter)

			->if($asserter->withArguments(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
				->exception(function() use ($asserter, & $failMessage) { $asserter->atLeastOnce($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testExactly()
	{
		$this
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
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
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()))

			->if(
				$call = new test\adapter\call('md5'),
				$adapter->md5($arg = uniqid())
			)
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))

			->if($adapter->md5($otherArg = uniqid()))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($asserter)

			->if($adapter->md5($anOtherArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($anOtherArg)))

			->if(
				$adapter->resetCalls(),
				$asserter->withArguments($arg = uniqid())
			)
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()))

			->if($adapter->md5($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))

			->if($adapter->md5($arg))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))

			->if($adapter->md5($arg))
			->then
				->object($asserter->exactly(2))->isIdenticalTo($asserter)

			->if($adapter->md5($arg))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)) . PHP_EOL . '[3] ' . $call->setArguments(array($arg))  . PHP_EOL . '[4] ' . $call->setArguments(array($arg)))
		;
	}

	public function testNever()
	{
		$this
			->if($asserter =$this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')
				->exception(function() use ($asserter) { $asserter->never; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')
				->exception(function() use ($asserter) { $asserter->never; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$call = new test\adapter\call('md5'),
				$asserter->call('md5')
			)
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
				->object($asserter->never)->isIdenticalTo($asserter)

			->if($adapter->md5($usedArg = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
				->exception(function() use ($asserter) { $asserter->never; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))

			->if(
				$adapter->resetCalls(),
				$asserter->withArguments($arg = uniqid())
			)
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
				->object($asserter->never)->isIdenticalTo($asserter)

			->if($adapter->md5($arg))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
				->exception(function() use ($asserter) { $asserter->never; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))

			->if($adapter->md5($arg))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
				->exception(function() use ($asserter) { $asserter->never; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 2 times instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
				->exception(function() use ($asserter, & $failMessage) { $asserter->never($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($asserter->withArguments(uniqid()))
			->then
				->object($asserter->never())->isIdenticalTo($asserter)
				->object($asserter->never)->isIdenticalTo($asserter)
		;
	}

	public function testBefore()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance($generator = new atoum\asserter\generator()),
				$adapter = new test\adapter(),
				$adapter->shouldBeCallBefore = uniqid(),
				$asserter->setWith($adapter),
				$beforeAsserter = $this->newTestedInstance(new atoum\asserter\generator()),
				$beforeAdapter = new test\adapter(),
				$beforeAdapter->wasCalledAfter = uniqid(),
				$beforeAsserter->setWith($beforeAdapter),
				$asserter->call('shouldBeCallBefore')->before($beforeAsserter->call('wasCalledAfter'))
			)

			->if(
				$adapter->shouldBeCallBefore(),
				$beforeAdapter->wasCalledAfter()
			)
			->then
				->object($asserter->once())->isIdenticalTo($asserter)

			->if(
				$adapter->resetCalls(),
				$beforeAdapter->resetCalls(),
				$beforeAdapter->wasCalledAfter(),
				$adapter->shouldBeCallBefore()
			)
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not called before %s'), $asserter->getCall(), $beforeAsserter->getCall()))

			->if(
				$adapter->resetCalls(),
				$beforeAdapter->resetCalls(),
				$beforeAdapter->wasCalledAfter(),
				$beforeAdapter->wasCalledAfter(),
				$adapter->shouldBeCallBefore()
			)
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not called before %s'), $asserter->getCall(), $beforeAsserter->getCall()))

			->if(
				$adapter->resetCalls(),
				$beforeAdapter->resetCalls(),
				$adapter->shouldBeCallBefore(),
				$beforeAdapter->wasCalledAfter(),
				$beforeAdapter->wasCalledAfter(),
				$adapter->shouldBeCallBefore()
			)
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2 before %s'), $asserter->getCall(), $beforeAsserter->getCall()))

			->if(
				$adapter->resetCalls(),
				$beforeAdapter->resetCalls(),
				$adapter->shouldBeCallBefore(),
				$beforeAdapter->wasCalledAfter(),
				$adapter->shouldBeCallBefore(),
				$beforeAdapter->wasCalledAfter()
			)
			->then
				->exception(function() use ($asserter) { $asserter->once(); })

			->if(
				$adapter->resetCalls(),
				$beforeAdapter->resetCalls(),
				$adapter->shouldBeCallBefore(),
				$beforeAdapter->wasCalledAfter(),
				$beforeAdapter->wasCalledAfter()
			)
			->then
				->object($asserter->once())->isIdenticalTo($asserter)

			->if(
				$adapter->resetCalls(),
				$beforeAdapter->resetCalls(),
				$adapter->shouldBeCallBefore(),
				$adapter->shouldBeCallBefore(),
				$beforeAdapter->wasCalledAfter(),
				$beforeAdapter->wasCalledAfter()
			)
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)
		;
	}

	public function testAfter()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance($generator = new atoum\asserter\generator()),
				$adapter = new test\adapter(),
				$adapter->shouldBeCallafter = uniqid(),
				$asserter->setWith($adapter),
				$afterAsserter = $this->newTestedInstance(new atoum\asserter\generator()),
				$afterAdapter = new test\adapter(),
				$afterAdapter->wasCalledBefore = uniqid(),
				$afterAsserter->setWith($afterAdapter),
				$asserter->call('shouldBeCallAfter')->after($afterAsserter->call('wasCalledBefore')),
				$afterAdapter->wasCalledBefore(),
				$adapter->shouldBeCallAfter()
			)
			->then
				->object($asserter->once())->isIdenticalTo($asserter)

			->if(
				$adapter->resetCalls(),
				$afterAdapter->resetCalls(),
				$adapter->shouldBeCallAfter(),
				$afterAdapter->wasCalledBefore()
			)
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not called after %s'), $asserter->getCall(), $afterAsserter->getCall()))

			->if(
				$adapter->resetCalls(),
				$afterAdapter->resetCalls(),
				$adapter->shouldBeCallAfter(),
				$adapter->shouldBeCallAfter(),
				$afterAdapter->wasCalledBefore()
			)
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not called after %s'), $asserter->getCall(), $afterAsserter->getCall()))

			->if(
				$adapter->resetCalls(),
				$afterAdapter->resetCalls(),
				$adapter->shouldBeCallAfter(),
				$afterAdapter->wasCalledBefore(),
				$adapter->shouldBeCallAfter()
			)
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2 after %s'), $asserter->getCall(), $afterAsserter->getCall()))

			->if(
				$adapter->resetCalls(),
				$afterAdapter->resetCalls(),
				$afterAdapter->wasCalledBefore(),
				$adapter->shouldBeCallAfter(),
				$afterAdapter->wasCalledBefore(),
				$adapter->shouldBeCallAfter()
			)
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)

			->if(
				$adapter->resetCalls(),
				$afterAdapter->resetCalls(),
				$afterAdapter->wasCalledBefore(),
				$adapter->shouldBeCallAfter(),
				$afterAdapter->wasCalledBefore()
			)
			->then
				->object($asserter->once())->isIdenticalTo($asserter)

			->if(
				$adapter->resetCalls(),
				$afterAdapter->resetCalls(),
				$afterAdapter->wasCalledBefore(),
				$adapter->shouldBeCallAfter(),
				$afterAdapter->wasCalledBefore(),
				$adapter->shouldBeCallAfter()
			)
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)

			->if(
				$adapter->resetCalls(),
				$afterAdapter->resetCalls(),
				$afterAdapter->wasCalledBefore(),
				$adapter->shouldBeCallAfter(),
				$adapter->shouldBeCallAfter(),
				$afterAdapter->wasCalledBefore()
			)
			->then
				->object($asserter->twice())->isIdenticalTo($asserter)

			->if(
				$adapter->resetCalls(),
				$afterAdapter->resetCalls(),
				$afterAdapter->wasCalledBefore(),
				$afterAdapter->wasCalledBefore(),
				$adapter->shouldBeCallAfter()
			)
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
		;
	}
}
