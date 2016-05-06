<?php

namespace
{
	function dummy() {}
}

namespace mageekguy\atoum\tests\units\test
{
	use
		mageekguy\atoum\test,
		mageekguy\atoum\test\adapter\call,
		mageekguy\atoum\test\adapter as testedClass
	;

	require_once __DIR__ . '/../../runner.php';

	class adapter extends test
	{
		public function testClass()
		{
			$this->testedClass->extends('mageekguy\atoum\adapter');
		}

		public function test__construct()
		{
			$this
				->if($adapter = new testedClass())
				->and($storage = new test\adapter\storage())
				->then
					->array($adapter->getInvokers())->isEmpty()
					->object($adapter->getCalls())->isEqualTo(new test\adapter\calls())
					->boolean($storage->contains($adapter))->isFalse()
				->if(testedClass::setStorage($storage))
				->and($otherAdapter = new testedClass())
				->then
					->array($otherAdapter->getInvokers())->isEmpty()
					->object($otherAdapter->getCalls())->isEqualTo(new test\adapter\calls())
					->boolean($storage->contains($adapter))->isFalse()
					->boolean($storage->contains($otherAdapter))->isTrue()
			;
		}

		public function test__clone()
		{
			$this
				->if($adapter = new testedClass())
				->and($storage = new test\adapter\storage())
				->and($clone = clone $adapter)
				->then
					->object($clone->getCalls())->isCloneOf($adapter->getCalls())
					->boolean($storage->contains($clone))->isFalse()
				->if(testedClass::setStorage($storage))
				->and($otherClone = clone $adapter)
				->then
					->object($otherClone->getCalls())->isCloneOf($adapter->getCalls())
					->boolean($storage->contains($clone))->isFalse()
					->boolean($storage->contains($otherClone))->isTrue()
			;
		}

		public function test__set()
		{
			$this
				->if($adapter = new testedClass())
				->and($adapter->md5 = $closure = function() {})
				->then
					->object($adapter->md5->getClosure())->isIdenticalTo($closure)
				->if($adapter->md5 = $return = uniqid())
				->then
					->object($adapter->md5)->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
					->object($adapter->MD5)->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
					->string($adapter->invoke('md5'))->isEqualTo($return)
					->string($adapter->invoke('MD5'))->isEqualTo($return)
				->if($adapter->MD5 = $return = uniqid())
				->then
					->object($adapter->md5)->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
					->object($adapter->MD5)->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
					->string($adapter->invoke('md5'))->isEqualTo($return)
					->string($adapter->invoke('MD5'))->isEqualTo($return)
			;
		}

		public function test__get()
		{
			$this
				->if($adapter = new testedClass())
				->and($adapter->md5 = $closure = function() {})
				->then
					->object($adapter->md5->getClosure())->isIdenticalTo($closure)
					->object($adapter->MD5->getClosure())->isIdenticalTo($closure)
				->if($adapter->md5 = uniqid())
				->then
					->object($adapter->md5->getClosure())->isInstanceOf('closure')
					->object($adapter->MD5->getClosure())->isInstanceOf('closure')
			;
		}

		public function test__isset()
		{
			$this
				->if($adapter = new testedClass())
				->then
					->boolean(isset($adapter->md5))->isFalse()
				->if($adapter->{$function = strtolower(uniqid())} = function() {})
				->then
					->boolean(isset($adapter->{$function}))->isTrue()
					->boolean(isset($adapter->{strtoupper($function)}))->isTrue()
				->if($adapter->{$function = strtoupper(uniqid())} = function() {})
				->then
					->boolean(isset($adapter->{strtolower($function)}))->isTrue()
					->boolean(isset($adapter->{$function}))->isTrue()
				->if($adapter->{$function = strtolower(uniqid())} = uniqid())
				->then
					->boolean(isset($adapter->{$function}))->isTrue()
					->boolean(isset($adapter->{strtoupper($function)}))->isTrue()
				->if($adapter->{$function = strtoupper(uniqid())} = uniqid())
				->then
					->boolean(isset($adapter->{$function}))->isTrue()
					->boolean(isset($adapter->{strtolower($function)}))->isTrue()
				->if($adapter->{$function = 'dummy'}[2] = uniqid())
				->then
					->boolean(isset($adapter->{$function}))->isFalse()
				->if($adapter->{$function}())
				->then
					->boolean(isset($adapter->{$function}))->isTrue()
				->if($adapter->{$function}())
				->then
					->boolean(isset($adapter->{$function}))->isFalse()
			;
		}

		public function test__unset()
		{
			$this
				->if($adapter = new testedClass())
				->then
					->array($adapter->getInvokers())->isEmpty()
					->array($adapter->getCalls()->toArray())->isEmpty()
				->when(function() use ($adapter) { unset($adapter->md5); })
					->array($adapter->getInvokers())->isEmpty()
					->array($adapter->getCalls()->toArray())->isEmpty()
				->when(function() use ($adapter) { unset($adapter->MD5); })
					->array($adapter->getInvokers())->isEmpty()
					->array($adapter->getCalls()->toArray())->isEmpty()
				->when(function() use ($adapter) { $adapter->md5 = uniqid(); $adapter->md5(uniqid()); })
					->array($adapter->getInvokers())->isNotEmpty()
					->array($adapter->getCalls()->toArray())->isNotEmpty()
				->when(function() use ($adapter) { unset($adapter->{uniqid()}); })
					->array($adapter->getInvokers())->isNotEmpty()
					->array($adapter->getCalls()->toArray())->isNotEmpty()
				->when(function() use ($adapter) { unset($adapter->md5); })
					->array($adapter->getInvokers())->isEmpty()
					->array($adapter->getCalls()->toArray())->isEmpty()
				->when(function() use ($adapter) { $adapter->MD5 = uniqid(); $adapter->MD5(uniqid()); })
					->array($adapter->getInvokers())->isNotEmpty()
					->array($adapter->getCalls()->toArray())->isNotEmpty()
				->when(function() use ($adapter) { unset($adapter->{uniqid()}); })
					->array($adapter->getInvokers())->isNotEmpty()
					->array($adapter->getCalls()->toArray())->isNotEmpty()
				->when(function() use ($adapter) { unset($adapter->MD5); })
					->array($adapter->getInvokers())->isEmpty()
					->array($adapter->getCalls()->toArray())->isEmpty()
			;
		}

		public function test__call()
		{
			$this
				->if($adapter = new testedClass())
				->then
					->string($adapter->md5($hash = uniqid()))->isEqualTo(md5($hash))
					->string($adapter->MD5($hash = uniqid()))->isEqualTo(md5($hash))
				->if($adapter->md5 = $md5 = uniqid())
				->then
					->string($adapter->md5($hash))->isEqualTo($md5)
					->string($adapter->MD5($hash))->isEqualTo($md5)
				->if($adapter->md5 = $md5 = uniqid())
				->then
					->string($adapter->md5($hash))->isEqualTo($md5)
					->string($adapter->MD5($hash))->isEqualTo($md5)
					->exception(function() use ($adapter) {
								$adapter->require(uniqid());
							}
						)
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Function \'require()\' is not invokable by an adapter')
					->exception(function() use ($adapter) {
								$adapter->REQUIRE(uNiqid());
							}
						)
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Function \'REQUIRE()\' is not invokable by an adapter')
				->if($adapter->md5 = 0)
				->and($adapter->md5[1] = 1)
				->and($adapter->md5[2] = 2)
				->and($adapter->resetCalls())
				->then
					->integer($adapter->md5())->isEqualTo(1)
					->integer($adapter->md5())->isEqualTo(2)
					->integer($adapter->md5())->isEqualTo(0)
				->if($adapter->resetCalls())
				->then
					->integer($adapter->MD5())->isEqualTo(1)
					->integer($adapter->MD5())->isEqualTo(2)
					->integer($adapter->MD5())->isEqualTo(0)
				->if($adapter->MD5 = 0)
				->and($adapter->MD5[1] = 1)
				->and($adapter->MD5[2] = 2)
				->and($adapter->resetCalls())
				->then
					->integer($adapter->md5())->isEqualTo(1)
					->integer($adapter->md5())->isEqualTo(2)
					->integer($adapter->md5())->isEqualTo(0)
				->if($adapter->resetCalls())
				->then
					->integer($adapter->MD5())->isEqualTo(1)
					->integer($adapter->MD5())->isEqualTo(2)
					->integer($adapter->MD5())->isEqualTo(0)
				->if($adapter = new testedClass())
				->and($adapter->sha1[2] = $sha1 = uniqid())
				->then
					->string($adapter->sha1($string = uniqid()))->isEqualTo(sha1($string))
					->string($adapter->sha1(uniqid()))->isEqualTo($sha1)
					->string($adapter->sha1($otherString = uniqid()))->isEqualTo(sha1($otherString))
			;
		}

		public function test__sleep()
		{
			$this
				->if($adapter = new testedClass())
				->then
					->array($adapter->__sleep())->isEmpty()
			;
		}

		public function test__toString()
		{
			$this
				->if($adapter = new testedClass())
				->and($calls = new test\adapter\calls())
				->then
					->castToString($adapter)->isEqualTo((string) $calls)
			;
		}

		public function testSerialize()
		{
			$this
				->if($adapter = new testedClass())
				->then
					->string(serialize($adapter))->isNotEmpty()
				->if($adapter->md5 = function() {})
				->then
					->string(serialize($adapter))->isNotEmpty()
			;
		}

		public function testSetCalls()
		{
			$this
				->if($adapter = new testedClass())
				->then
					->object($adapter->setCalls($calls = new test\adapter\calls()))->isIdenticalTo($adapter)
					->object($adapter->getCalls())->isIdenticalTo($calls)
					->object($adapter->setCalls())->isIdenticalTo($adapter)
					->object($adapter->getCalls())
						->isNotIdenticalTo($calls)
						->isEqualTo(new test\adapter\calls())
				->if($calls = new test\adapter\calls())
				->and($calls[] = new test\adapter\call(uniqid()))
				->and($adapter->setCalls($calls))
				->then
					->object($adapter->getCalls())
						->isIdenticalTo($calls)
						->hasSize(0)
			;
		}

		public function testGetCalls()
		{
			$this
				->if($adapter = new testedClass())
				->and($adapter->setCalls($calls = new \mock\mageekguy\atoum\test\adapter\calls()))
				->and($this->calling($calls)->get = $innerCalls = new test\adapter\calls())
				->then
					->object($adapter->getCalls())->isIdenticalTo($calls)
					->object($adapter->getCalls($call = new test\adapter\call(uniqid())))->isIdenticalTo($innerCalls)
					->mock($calls)->call('get')->withArguments($call, false)->once()
			;
		}

		public function testGetCallsEqualTo()
		{
			$this
				->if($calls = new \mock\mageekguy\atoum\test\adapter\calls())
				->and($this->calling($calls)->getEqualTo = $equalCalls = new test\adapter\calls())
				->and($adapter = new testedClass())
				->and($adapter->setCalls($calls))
				->then
					->object($adapter->getCallsEqualTo($call = new call('md5')))->isIdenticalTo($equalCalls)
					->mock($calls)->call('getEqualTo')->withArguments($call)->once()
			;
		}

		public function testGetPreviousCalls()
		{
			$this
				->if($calls = new \mock\mageekguy\atoum\test\adapter\calls())
				->and($this->calling($calls)->getPrevious = $previousCalls = new test\adapter\calls())
				->and($adapter = new testedClass())
				->and($adapter->setCalls($calls))
				->then
					->object($adapter->getPreviousCalls($call = new call('md5'), $position = rand(1, PHP_INT_MAX)))->isIdenticalTo($previousCalls)
					->mock($calls)->call('getPrevious')->withArguments($call, $position, false)->once()
					->object($adapter->getPreviousCalls($call = new call('md5'), $position = rand(1, PHP_INT_MAX), true))->isIdenticalTo($previousCalls)
					->mock($calls)->call('getPrevious')->withArguments($call, $position, true)->once()
			;
		}

		public function testHasPreviousCalls()
		{
			$this
				->if($calls = new \mock\mageekguy\atoum\test\adapter\calls())
				->and($this->calling($calls)->hasPrevious = $has = (boolean) rand(0, 1))
				->and($adapter = new testedClass())
				->and($adapter->setCalls($calls))
				->then
					->boolean($adapter->hasPreviousCalls($call = new call('md5'), $position = rand(1, PHP_INT_MAX)))->isEqualTo($has)
					->mock($calls)->call('hasPrevious')->withArguments($call, $position, false)->once()
					->boolean($adapter->hasPreviousCalls($call = new call('md5'), $position = rand(1, PHP_INT_MAX), true))->isEqualTo($has)
					->mock($calls)->call('hasPrevious')->withArguments($call, $position, true)->once()
			;
		}

		public function testGetAfterCalls()
		{
			$this
				->if($calls = new \mock\mageekguy\atoum\test\adapter\calls())
				->and($this->calling($calls)->getAfter = $afterCalls = new test\adapter\calls())
				->and($adapter = new testedClass())
				->and($adapter->setCalls($calls))
				->then
					->object($adapter->getAfterCalls($call = new call('md5'), $position = rand(1, PHP_INT_MAX)))->isIdenticalTo($afterCalls)
					->mock($calls)->call('getAfter')->withArguments($call, $position, false)->once()
					->object($adapter->getAfterCalls($call = new call('md5'), $position = rand(1, PHP_INT_MAX), true))->isIdenticalTo($afterCalls)
					->mock($calls)->call('getAfter')->withArguments($call, $position, true)->once()
			;
		}

		public function testHasAfterCalls()
		{
			$this
				->if($calls = new \mock\mageekguy\atoum\test\adapter\calls())
				->and($this->calling($calls)->hasAfter = $has = (boolean) rand(0, 1))
				->and($adapter = new testedClass())
				->and($adapter->setCalls($calls))
				->then
					->boolean($adapter->hasAfterCalls($call = new call('md5'), $position = rand(1, PHP_INT_MAX)))->isEqualTo($has)
					->mock($calls)->call('hasAfter')->withArguments($call, $position, false)->once()
					->boolean($adapter->hasAfterCalls($call = new call('md5'), $position = rand(1, PHP_INT_MAX), true))->isEqualTo($has)
					->mock($calls)->call('hasAfter')->withArguments($call, $position, true)->once()
			;
		}

		public function testGetCallsIdenticalTo()
		{
			$this
				->if($calls = new \mock\mageekguy\atoum\test\adapter\calls())
				->and($this->calling($calls)->getIdenticalTo = $identicalCalls = new test\adapter\calls())
				->and($adapter = new testedClass())
				->and($adapter->setCalls($calls))
				->then
					->object($adapter->getCallsIdenticalTo($call = new call('md5')))->isIdenticalTo($identicalCalls)
					->mock($calls)->call('getIdenticalTo')->withArguments($call)->once()
			;
		}

		public function testGetCallNumber()
		{
			$this
				->if($calls = new \mock\mageekguy\atoum\test\adapter\calls())
				->and($this->calling($calls)->count = 0)
				->and($adapter = new testedClass())
				->and($adapter->setCalls($calls))
				->then
					->integer($adapter->getCallNumber())->isZero()
				->and($this->calling($calls)->count = $callNumber = rand(1, PHP_INT_MAX))
				->then
					->integer($adapter->getCallNumber())->isEqualTo($callNumber)
			;
		}

		public function testGetTimeline()
		{
			$this
				->if($adapter = new testedClass())
				->and($adapter->setCalls($calls = new \mock\mageekguy\atoum\test\adapter\calls()))
				->and($this->calling($calls)->getTimeline = array())
				->then
					->array($adapter->getTimeline())->isEmpty()
					->mock($calls)->call('getTimeline')->withArguments(null, false)->once()
			;
		}

		public function testAddCall()
		{
			$this
				->if($adapter = new testedClass())
				->and($adapter->setCalls($calls = new \mock\mageekguy\atoum\test\adapter\calls()))
				->and($this->calling($calls)->addCall = $calls)
				->then
					->object($adapter->addCall($method = uniqid(), $args = array(uniqid())))->isIdenticalTo($adapter)
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call($method, $args))->once()
					->object($adapter->addCall($otherMethod = uniqid(), $otherArgs = array(uniqid(), uniqid())))->isIdenticalTo($adapter)
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call($otherMethod, $otherArgs))->once()
					->object($adapter->addCall($method, $anotherArgs = array(uniqid())))->isIdenticalTo($adapter)
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call($method, $anotherArgs))->once()
				->if($arg = 'foo')
				->and($arguments = array(& $arg))
				->then
					->object($adapter->addCall($method, $arguments))->isIdenticalTo($adapter)
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call($method, $arguments))->once()
			;
		}

		public function testResetCalls()
		{
			$this
				->if(
					$adapter = new testedClass(),
					$adapter->md5(uniqid()),
					$adapter->sha1(uniqid())
				)
				->then
					->sizeof($adapter->getCalls())->isEqualTo(2)
					->sizeof($adapter->getCalls(new call('md5')))->isEqualTo(1)
					->sizeof($adapter->getCalls(new call('sha1')))->isEqualTo(1)
					->object($adapter->resetCalls())->isIdenticalTo($adapter)
					->sizeof($adapter->getCalls())->isZero
				->if(
					$adapter->md5(uniqid()),
					$adapter->sha1(uniqid())
				)
				->then
					->sizeof($adapter->getCalls())->isEqualTo(2)
					->sizeof($adapter->getCalls(new call('md5')))->isEqualTo(1)
					->sizeof($adapter->getCalls(new call('sha1')))->isEqualTo(1)
					->object($adapter->resetCalls('md5'))->isIdenticalTo($adapter)
					->sizeof($adapter->getCalls())->isEqualTo(1)
					->sizeof($adapter->getCalls(new call('md5')))->isZero
					->sizeof($adapter->getCalls(new call('sha1')))->isEqualTo(1)
					->object($adapter->resetCalls('sha1'))->isIdenticalTo($adapter)
					->sizeof($adapter->getCalls(new call('md5')))->isZero
					->sizeof($adapter->getCalls(new call('sha1')))->isZero
					->sizeof($adapter->getCalls())->isZero
			;
		}

		public function testReset()
		{
			$this
				->if($adapter = new testedClass())
				->then
					->array($adapter->getInvokers())->isEmpty()
					->sizeof($adapter->getCalls())->isZero()
					->object($adapter->reset())->isIdenticalTo($adapter)
					->array($adapter->getInvokers())->isEmpty()
					->sizeof($adapter->getCalls())->isZero()
				->if($adapter->md5(uniqid()))
				->then
					->array($adapter->getInvokers())->isEmpty()
					->sizeof($adapter->getCalls())->isGreaterThan(0)
					->object($adapter->reset())->isIdenticalTo($adapter)
					->array($adapter->getInvokers())->isEmpty()
					->sizeof($adapter->getCalls())->isZero()
				->if($adapter->md5 = uniqid())
				->then
					->array($adapter->getInvokers())->isNotEmpty()
					->sizeof($adapter->getCalls())->isZero(0)
					->object($adapter->reset())->isIdenticalTo($adapter)
					->array($adapter->getInvokers())->isEmpty()
					->sizeof($adapter->getCalls())->isZero()
				->if($adapter->md5 = uniqid())
				->and($adapter->md5(uniqid()))
				->then
					->array($adapter->getInvokers())->isNotEmpty()
					->sizeof($adapter->getCalls())->isGreaterThan(0)
					->object($adapter->reset())->isIdenticalTo($adapter)
					->array($adapter->getInvokers())->isEmpty()
					->sizeof($adapter->getCalls())->isZero()
			;
		}

		public function testGetCallsNumber()
		{
			$this
				->given($this->newTestedInstance)
				->then
					->integer($this->testedInstance->getCallsNumber(new call('md5')))->isZero
				->if(
					$this->testedInstance->md5(uniqid()),
					$this->testedInstance->sha1(uniqid())
				)
				->then
					->integer($this->testedInstance->getCallsNumber())->isEqualTo(2)
					->integer($this->testedInstance->getCallsNumber(new call('md5')))->isEqualTo(1)
					->integer($this->testedInstance->getCallsNumber(new call('sha1')))->isEqualTo(1)
				->given(
					$castable = new \mock\castable,
					$this->calling($castable)->__toString = $string = uniqid()
				)
				->if(
					$this->testedInstance->resetCalls(),
					$this->testedInstance->md5(1),
					$this->testedInstance->md5('1')
				)
				->then
					->integer($this->testedInstance->getCallsNumber())->isEqualTo(2)
					->integer($this->testedInstance->getCallsNumber(new call('md5')))->isEqualTo(2)
					->integer($this->testedInstance->getCallsNumber(new call('md5'), true))->isEqualTo(2)
					->integer($this->testedInstance->getCallsNumber(new call('md5', array(1))))->isEqualTo(2)
					->integer($this->testedInstance->getCallsNumber(new call('md5', array(1)), true))->isEqualTo(1)
					->integer($this->testedInstance->getCallsNumber(new call('md5', array('1')), true))->isEqualTo(1)
			;
		}

		public function testGetCallsNumberEqualTo()
		{
			$this
				->given($this->newTestedInstance)
				->then
					->integer($this->testedInstance->getCallsNumber(new call('md5')))->isZero
				->if(
					$this->testedInstance->md5(uniqid()),
					$this->testedInstance->md5(1),
					$this->testedInstance->md5('1')
				)
				->then
					->integer($this->testedInstance->getCallsNumberEqualTo(new call('md5')))->isEqualTo(3)
					->integer($this->testedInstance->getCallsNumberEqualTo(new call('md5', array(1))))->isEqualTo(2)
					->integer($this->testedInstance->getCallsNumberEqualTo(new call('md5', array('1'))))->isEqualTo(2)
			;
		}
	}
}
