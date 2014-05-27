<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable
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
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new atoum\asserter\generator())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new atoum\tools\variable\analyzer())
				->variable($this->testedInstance->getAdapter())->isNull()
				->variable($this->testedInstance->getCall())->isEqualTo(new test\adapter\call())

			->given($this->newTestedInstance($generator = new atoum\asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getAnalyzer())->isEqualTo($analyzer)
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
				->variable($this->testedInstance->getAdapter())->isNull()
				->variable($this->testedInstance->getCall())->isEqualTo(new test\adapter\call())
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
				->object($this->testedInstance->reset())->isTestedInstance
				->variable($this->testedInstance->getAdapter())->isNull()

			->if($this->testedInstance->setWith($adapter = new atoum\test\adapter()))
			->then
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
				->sizeOf($adapter->getCalls())->isZero()
				->object($this->testedInstance->reset())->isTestedInstance
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
				->sizeOf($adapter->getCalls())->isZero()

			->if($adapter->md5(uniqid()))
			->then
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
				->sizeOf($adapter->getCalls())->isEqualTo(1)
				->object($this->testedInstance->reset())->isTestedInstance
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
				->object($this->testedInstance->call($function = uniqid()))->isTestedInstance
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))

			->if($this->testedInstance->withArguments())
			->then
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, array()))
				->object($this->testedInstance->disableEvaluationChecking()->call($function = uniqid()))->isTestedInstance
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
				->object($this->testedInstance->withArguments())->isTestedInstance
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, array()))
				->object($this->testedInstance->withArguments($arg1 = uniqid()))->isTestedInstance
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, array($arg1)))
				->object($this->testedInstance->disableEvaluationChecking()->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isTestedInstance
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
				->object($this->testedInstance->withAnyArguments())->isTestedInstance
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))

			->if($this->testedInstance->disableEvaluationChecking()->withArguments($arg = uniqid()))
			->then
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, array($arg)))
				->object($this->testedInstance->withAnyArguments())->isTestedInstance
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
				->object($this->testedInstance->disableEvaluationChecking()->withoutAnyArgument())->isTestedInstance
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

				->exception(function() use ($asserter) { $asserter->oNCE; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new \mock\atoum\test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->oNCE; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
				$this->calling($calls)->count = 0,
				$this->calling($call)->__toString = $callAsString = uniqid(),
				$this->calling($locale)->__ = $notCalled = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 1)->once

				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 1)->twice

				->exception(function() use ($asserter) { $asserter->OncE; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 1)->thrice

				->exception(function() use ($asserter, & $failMessage) { $asserter->once($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->calling($calls)->count = 1)
			->then
				->object($this->testedInstance->once())->isTestedInstance
				->object($this->testedInstance->once)->isTestedInstance
				->object($this->testedInstance->oNCE)->isTestedInstance

			->if(
				$this->calling($calls)->count = $count = rand(2, PHP_INT_MAX),
				$this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
				$this->calling($callsEqualTo)->count = rand(1, PHP_INT_MAX),
				$this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 1)->once

				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 1)->twice

				->exception(function() use ($asserter) { $asserter->OncE; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 1)->thrice

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

				->exception(function() use ($asserter) { $asserter->TWICe; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new \mock\atoum\test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->twICE; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
				$this->calling($calls)->count = 0,
				$this->calling($call)->__toString = $callAsString = uniqid(),
				$this->calling($locale)->__ = $notCalled = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 2)->once

				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 2)->twice

				->exception(function() use ($asserter) { $asserter->TWICe; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 2)->thrice

				->exception(function() use ($asserter, & $failMessage) { $asserter->twice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if(
				$this->calling($calls)->count = 1,
				$this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
				$this->calling($callsEqualTo)->count = 1,
				$this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 1, $callAsString, 1, 2)->once

				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 1, $callAsString, 1, 2)->twice

				->exception(function() use ($asserter) { $asserter->TWICe; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 1, $callAsString, 1, 2)->thrice

				->exception(function() use ($asserter, & $failMessage) { $asserter->twice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->calling($calls)->count = 2)
			->then
				->object($this->testedInstance->twice())->isTestedInstance
				->object($this->testedInstance->twice)->isTestedInstance
				->object($this->testedInstance->TWICe)->isTestedInstance

			->if(
				$this->calling($calls)->count = $count = rand(3, PHP_INT_MAX),
				$this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
				$this->calling($callsEqualTo)->count = rand(1, PHP_INT_MAX),
				$this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 2)->once

				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 2)->twice

				->exception(function() use ($asserter) { $asserter->TWICe; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 2)->thrice

				->exception(function() use ($asserter, & $failMessage) { $asserter->once($failMessage = uniqid()); })
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

				->exception(function() use ($asserter) { $asserter->tHRICe; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new \mock\atoum\test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->thRICE; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
				$this->calling($calls)->count = 0,
				$this->calling($call)->__toString = $callAsString = uniqid(),
				$this->calling($locale)->__ = $notCalled = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 3)->once

				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 3)->twice

				->exception(function() use ($asserter) { $asserter->THRIce; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 3)->thrice

				->exception(function() use ($asserter, & $failMessage) { $asserter->thrice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if(
				$this->calling($calls)->count = $count = rand(1, 2),
				$this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
				$this->calling($callsEqualTo)->count = $count,
				$this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->once

				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->twice

				->exception(function() use ($asserter) { $asserter->tHRICe; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->thrice

				->exception(function() use ($asserter, & $failMessage) { $asserter->thrice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->calling($calls)->count = 3)
			->then
				->object($this->testedInstance->thrice())->isTestedInstance
				->object($this->testedInstance->thrice)->isTestedInstance
				->object($this->testedInstance->THRIcE)->isTestedInstance

			->if(
				$this->calling($calls)->count = $count = rand(3, PHP_INT_MAX),
				$this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
				$this->calling($callsEqualTo)->count = $count,
				$this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->once

				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->twice

				->exception(function() use ($asserter) { $asserter->tHRICe; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->thrice

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

				->exception(function() use ($asserter) { $asserter->atLEASToNce; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->if($asserter->setWith($adapter = new \mock\atoum\test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->atLeastOnce; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->atLeASTonce; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
				$this->calling($calls)->count = 0,
				$this->calling($call)->__toString = $callAsString = uniqid(),
				$this->calling($locale)->_ = $notCalled = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('_')->withArguments('%s is called 0 time', $callAsString)->once

				->exception(function() use ($asserter) { $asserter->atLeastOnce; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('_')->withArguments('%s is called 0 time', $callAsString)->twice

				->exception(function() use ($asserter) { $asserter->atLEASToNCE; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('_')->withArguments('%s is called 0 time', $callAsString)->thrice

				->exception(function() use ($asserter, & $failMessage) { $asserter->atLeastOnce($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->calling($calls)->count = rand(1, PHP_INT_MAX))
			->then
				->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
				->object($asserter->atLeastOnce)->isIdenticalTo($asserter)
				->object($asserter->atLEASToNCe)->isIdenticalTo($asserter)
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

			->if($asserter->setWith($adapter = new \mock\atoum\test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
				$this->calling($calls)->count = 0,
				$this->calling($call)->__toString = $callAsString = uniqid(),
				$this->calling($locale)->__ = $notCalled = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $callNumber) { $asserter->exactly($callNumber = rand(1, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, $callNumber)->once

				->exception(function() use ($asserter, & $callNumber, & $failMessage) { $asserter->exactly($callNumber = rand(1, PHP_INT_MAX), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->object($this->testedInstance->exactly(0))->isTestedInstance

			->if(
				$this->calling($calls)->count = $count = rand(1, PHP_INT_MAX),
				$this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
				$this->calling($callsEqualTo)->count = $count,
				$this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->exactly(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualToAsString)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 0)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->exactly(0, $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->object($this->testedInstance->exactly($count))->isTestedInstance
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

			->if($asserter->setWith($adapter = new \mock\atoum\test\adapter()))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->never; })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
				$this->calling($calls)->count = $count = rand(1, PHP_INT_MAX),
				$this->calling($call)->__toString = $callAsString = uniqid(),
				$this->calling($locale)->__ = $wasCalled = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $callNumber) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($wasCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 0)->once

				->exception(function() use ($asserter, & $callNumber) { $asserter->never; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($wasCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 0)->twice

				->exception(function() use ($asserter, & $callNumber) { $asserter->NEvEr; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($wasCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 0)->thrice

				->exception(function() use ($asserter, & $failMessage) { $asserter->never($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->calling($calls)->count = 0)
			->then
				->object($this->testedInstance->never())->isTestedInstance
				->object($this->testedInstance->never)->isTestedInstance
				->object($this->testedInstance->nEVER)->isTestedInstance
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
