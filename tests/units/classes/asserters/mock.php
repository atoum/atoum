<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable,
	mageekguy\atoum\test\adapter\call\decorators
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
		$this->testedClass->extends('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->then
			->if($this->newTestedInstance)
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call())
				->variable($this->testedInstance->getAdapter())->isNull
				->variable($this->testedInstance->getLastAssertionFile())->isNull
				->variable($this->testedInstance->getLastAssertionLine())->isNull

			->if($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
				->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call())
				->variable($this->testedInstance->getAdapter())->isNull
				->variable($this->testedInstance->getLastAssertionFile())->isNull
				->variable($this->testedInstance->getLastAssertionLine())->isNull
		;
	}

	public function testReset()
	{
		$this
			->given(
				$mockController = new \mock\mageekguy\atoum\mock\controller(),
				$asserter = $this->newTestedInstance
			)
			->then
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getAdapter())->isNull()

			->if(
				$asserter->setWith($mock = new \mock\mageekguy\atoum\score()),
				$mock->setMockController($mockController),
				$this->resetMock($mockController)
			)
			->then
				->object($asserter->reset())->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isIdenticalTo($mock->getMockController())
				->mock($mockController)->call('resetCalls')->once();
	}

	public function testSetWith()
	{
		$this
			->given($asserter = $this->newTestedInstance)

			->if($asserter
					->setLocale($locale = new \mock\atoum\locale())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer()),
				$this->calling($locale)->_ = $notMock = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $mock) { $asserter->setWith($mock = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notMock)
				->mock($locale)->call('_')->withArguments('%s is not a mock', $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments($mock)->once

				->object($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock()))->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isIdenticalTo($mock->getMockController())
		;
	}

	public function testWasCalled()
	{
		$this
			->if($asserter = $this->newTestedInstance)
				->then
					->exception(function() use ($asserter) { $asserter->wasCalled(); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Mock is undefined')

				->if($asserter
						->setWith($mock = new \mock\foo($controller = new \mock\atoum\mock\controller()))
						->setLocale($locale = new \mock\atoum\locale()),
					$this->calling($locale)->_ = $wasNotCalled = uniqid(),
					$this->calling($controller)->getCallsNumber = 0,
					$this->calling($controller)->getMockClass = $mockClass = uniqid()
				)
				->then
					->exception(function() use ($asserter) { $asserter->wasCalled(); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($wasNotCalled)
					->mock($locale)->call('_')->withArguments('%s is not called', $mockClass)->once

					->exception(function() use ($asserter) { $asserter->wasCalled; })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($wasNotCalled)
					->mock($locale)->call('_')->withArguments('%s is not called', $mockClass)->twice

					->exception(function() use ($asserter, & $failMessage) { $asserter->wasCalled($failMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($failMessage)

				->if($this->calling($controller)->getCallsNumber = rand(1, PHP_INT_MAX))
				->then
					->object($asserter->wasCalled())->isIdenticalTo($asserter)
					->object($asserter->wasCalled)->isIdenticalTo($asserter)
		;
	}

	public function testWasNotCalled()
	{
		$this
			->if($asserter = $this->newTestedInstance)
				->then
					->exception(function() use ($asserter) { $asserter->wasNotCalled(); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Mock is undefined')

				->if($asserter
						->setWith($mock = new \mock\foo($controller = new \mock\atoum\mock\controller()))
						->setLocale($locale = new \mock\atoum\locale()),
					$this->calling($locale)->_ = $wasCalled = uniqid(),
					$this->calling($controller)->getCallsNumber = rand(1, PHP_INT_MAX),
					$this->calling($controller)->getMockClass = $mockClass = uniqid()
				)
				->then
					->exception(function() use ($asserter) { $asserter->wasNotCalled(); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($wasCalled)
					->mock($locale)->call('_')->withArguments('%s is called', $mockClass)->once

					->exception(function() use ($asserter) { $asserter->wasNotCalled; })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($wasCalled)
					->mock($locale)->call('_')->withArguments('%s is called', $mockClass)->twice

					->exception(function() use ($asserter, & $failMessage) { $asserter->wasNotCalled($failMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($failMessage)

				->if($this->calling($controller)->getCallsNumber = 0)
				->then
					->object($asserter->wasNotCalled())->isIdenticalTo($asserter)
					->object($asserter->wasNotCalled)->isIdenticalTo($asserter)
		;
	}

	public function testCall()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->call(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given(
				$asserter->setManager($manager = new \mock\atoum\asserters\adapter\call\manager()),
				$mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()),
				$this->calling($mockController)->getMockClass = $mockClass = uniqid()
			)
			->if($asserter->setWith($mock))
			->then
				->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo(__LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, null, new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once

				->object($asserter->call($otherFunction = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo(__LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($otherFunction, null, new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->twice
		;
	}

	public function testReceive()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->receive(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given(
				$asserter->setManager($manager = new \mock\atoum\asserters\adapter\call\manager()),
				$mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()),
				$this->calling($mockController)->getMockClass = $mockClass = uniqid()
			)
			->if($asserter->setWith($mock))
			->then
				->object($asserter->receive($function = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo(__LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, null, new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->receive('add')->withArguments($asserter)->once

				->object($asserter->receive($otherFunction = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo(__LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($otherFunction, null, new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->receive('add')->withArguments($asserter)->twice
		;
	}

	public function testWithArguments()
	{
		$this
			->given($asserter = $this->newTestedInstance)

			->then
				->exception(function() use ($asserter) { $asserter->withArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given(
				$mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()),
				$this->calling($mockController)->getMockClass = $mockClass = uniqid()
			)
			->if($asserter->setWith($mock))
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter->setManager($manager = new \mock\atoum\asserters\adapter\call\manager()),
				$asserter->call($function = uniqid())
			)
			->then
				->object($asserter->withArguments())->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo(__LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array(), new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once

				->object($asserter->withArguments($arg1 = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo(__LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array($arg1),  new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once

				->object($asserter->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo($line = __LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array($arg1, $arg2),  new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once
		;
	}

	public function testWithAtLeastArguments()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->withAtLeastArguments(array(uniqid())); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given(
				$mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()),
				$this->calling($mockController)->getMockClass = $mockClass = uniqid()
			)
			->if($asserter->setWith($mock))
			->then
				->exception(function() use ($asserter) { $asserter->withAtLeastArguments(array(uniqid())); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter->setManager($manager = new \mock\atoum\asserters\adapter\call\manager()),
				$asserter->call($function = uniqid())
			)
			->then
				->object($asserter->withAtLeastArguments($arguments = array(1 => uniqid())))->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo($line = __LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, $arguments, new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once

				->object($asserter->disableEvaluationChecking()->withAtLeastArguments($arguments = array(2 => uniqid(), 5 => uniqid())))->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo($line = __LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, $arguments, new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once
		;
	}

	public function testWithAnyArguments()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->withAnyArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->withAnyArguments; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->WITHaNYaRGUMENts; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given(
				$mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()),
				$this->calling($mockController)->getMockClass = $mockClass = uniqid()
			)
			->if($asserter->setWith($mock))
			->then
				->exception(function() use ($asserter) { $asserter->withAnyArguments(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->withAnyArguments; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->wITHaNYArguments; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter->setManager($manager = new \mock\atoum\asserters\adapter\call\manager()),
				$asserter->call($function = uniqid())
			)
			->then
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo($line = __LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, null, new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once

				->object($asserter->withAnyArguments)->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo($line = __LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, null, new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once

			->if($asserter->withArguments(uniqid()))
			->then
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo($line = __LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, null, new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once

			->if($asserter->withArguments(uniqid()))
			->then
				->object($asserter->withAnyArguments)->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo($line = __LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, null, new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once
		;
	}

	public function testWithoutAnyArgument()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->withoutAnyArgument; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->witHOUTaNYaRGument; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given(
				$mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()),
				$this->calling($mockController)->getMockClass = $mockClass = uniqid()
			)
			->if($asserter->setWith($mock))
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->withoutAnyArgument; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->withoUTaNyArgumENT; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter->setManager($manager = new \mock\atoum\asserters\adapter\call\manager()),
				$asserter->call($function = uniqid())
			)
			->then
				->object($asserter->withoutAnyArgument())->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo($line = __LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array(), new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once

				->object($asserter->withoutAnyArgument)->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo($line = __LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array(), new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once

				->object($asserter->wITHOUTaNYaRGument)->isIdenticalTo($asserter)
				->string($asserter->getLastAssertionFile())->isEqualTo(__FILE__)
				->integer($asserter->getLastAssertionLine())->isEqualTo($line = __LINE__ - 2)
				->object($asserter->getCall())->isEqualTo(new test\adapter\call($function, array(), new decorators\addClass($mockClass)))
				->array($asserter->getBefore())->isEmpty
				->array($asserter->getAfter())->isEmpty
				->mock($manager)->call('add')->withArguments($asserter)->once
		;
	}

	public function testNever()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->never; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->NEVEr; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given($mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()))
			->if($asserter->setWith($mock))
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->never; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->nEVER; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($mockController)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
				$this->calling($calls)->count = $number = rand(1, PHP_INT_MAX),
				$this->calling($call)->__toString = $callAsString = uniqid(),
				$this->calling($locale)->__ = $notCalled = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 0)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->once($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->never; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 0)->twice

				->exception(function() use ($asserter) { $asserter->nEVER; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 0)->thrice

			->if($this->calling($calls)->count = 0)
			->then
				->object($this->testedInstance->never())->isTestedInstance
				->object($this->testedInstance->never)->isTestedInstance
				->object($this->testedInstance->nEVer)->isTestedInstance
		;
	}

	public function testOnce()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->oNCE; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given($mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()))
			->if($asserter->setWith($mock))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->oNCE; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($mockController)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
				$this->calling($calls)->count = 0,
				$this->calling($call)->__toString = $callAsString = uniqid(),
				$this->calling($locale)->__ = $notCalled = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 1)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->once($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 1)->twice

				->exception(function() use ($asserter) { $asserter->oNCE; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 1)->thrice

			->if($this->calling($calls)->count = $number = rand(2, PHP_INT_MAX))
			->then
				->exception(function() use ($asserter) { $asserter->once(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 1)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->once($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->once; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 1)->twice

				->exception(function() use ($asserter) { $asserter->oNCE; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 1)->thrice

			->if($this->calling($calls)->count = 1)
			->then
				->object($this->testedInstance->once())->isTestedInstance
				->object($this->testedInstance->once)->isTestedInstance
				->object($this->testedInstance->oNCE)->isTestedInstance
		;
	}

	public function testTwice()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->tWICe; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given($mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()))
			->if($asserter->setWith($mock))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->TWICe; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($mockController)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
				$this->calling($calls)->count = 0,
				$this->calling($call)->__toString = $callAsString = uniqid(),
				$this->calling($locale)->__ = $notCalled = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 2)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->twice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 2)->twice

				->exception(function() use ($asserter) { $asserter->tWiCE; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 2)->thrice

			->if($this->calling($calls)->count = $number = rand(3, PHP_INT_MAX))
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 2)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->twice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 2)->twice

				->exception(function() use ($asserter) { $asserter->tWICE; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 2)->thrice

			->if($this->calling($calls)->count = $number = 1)
			->then
				->exception(function() use ($asserter) { $asserter->twice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, 1, 2)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->twice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->twice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, 1, 2)->twice

				->exception(function() use ($asserter) { $asserter->tWICE; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, 1, 2)->thrice

			->if($this->calling($calls)->count = 2)
			->then
				->object($this->testedInstance->twice())->isTestedInstance
				->object($this->testedInstance->twice)->isTestedInstance
				->object($this->testedInstance->tWICE)->isTestedInstance
		;
	}

	public function testThrice()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->tHRICe; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given($mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()))
			->if($asserter->setWith($mock))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->THRICe; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($mockController)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
				$this->calling($calls)->count = 0,
				$this->calling($call)->__toString = $callAsString = uniqid(),
				$this->calling($locale)->__ = $notCalled = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 3)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->thrice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 3)->twice

				->exception(function() use ($asserter) { $asserter->tHRICE; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 3)->thrice

			->if($this->calling($calls)->count = $number = rand(4, PHP_INT_MAX))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 3)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->thrice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 3)->twice

				->exception(function() use ($asserter) { $asserter->tHRICe; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 3)->thrice

			->if($this->calling($calls)->count = $number = rand(1, 2))
			->then
				->exception(function() use ($asserter) { $asserter->thrice(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 3)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->thrice($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->thrice; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 3)->twice

				->exception(function() use ($asserter) { $asserter->tHRICe; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $number, $callAsString, $number, 3)->thrice

			->if($this->calling($calls)->count = 3)
			->then
				->object($this->testedInstance->thrice())->isTestedInstance
				->object($this->testedInstance->thrice)->isTestedInstance
				->object($this->testedInstance->thRICE)->isTestedInstance
		;
	}

	public function testAtLeastOnce()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->atLeastOnce; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

				->exception(function() use ($asserter) { $asserter->ATlEASToNCe; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given($mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()))
			->if($asserter->setWith($mock))
			->then
				->exception(function() use ($asserter) { $asserter->atLeastOnce(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->atLeastOnce; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

				->exception(function() use ($asserter) { $asserter->ATlEASToNCe; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($mockController)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
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
				->object($this->testedInstance->atLeastOnce())->isTestedInstance
				->object($this->testedInstance->atLeastOnce)->isTestedInstance
				->object($this->testedInstance->atLEASToNCe)->isTestedInstance
		;
	}

	public function testExactly()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->exactly(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')

			->given($mock = new \mock\foo($mockController = new \mock\atoum\mock\controller()))
			->if($asserter->setWith($mock))
			->then
				->exception(function() use ($asserter) { $asserter->exactly(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->call(uniqid())
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($mockController)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
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
				$this->calling($mockController)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
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
}
