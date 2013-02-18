<?php

namespace mageekguy\atoum\tests\units\test;

use
	mageekguy\atoum\test,
	mageekguy\atoum\test\adapter as testedClass,
	mageekguy\atoum\test\adapter\invoker,
	mageekguy\atoum\dependency,
	mageekguy\atoum\dependencies
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
			->then
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
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
			->if($adapter->{$function = uniqid()}[2] = uniqid())
			->then
				->boolean(isset($adapter->{$function}))->isFalse()
				->boolean(isset($adapter->{$function}[0]))->isFalse()
				->boolean(isset($adapter->{$function}[1]))->isFalse()
				->boolean(isset($adapter->{$function}[2]))->isTrue()
				->boolean(isset($adapter->{$function}[3]))->isFalse()
		;
	}

	public function test__unset()
	{
		$this
			->if($adapter = new testedClass())
			->then
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter) { unset($adapter->md5); })
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter) { unset($adapter->MD5); })
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter) { $adapter->md5 = uniqid(); $adapter->md5(uniqid()); })
				->array($adapter->getInvokers())->isNotEmpty()
				->array($adapter->getCalls())->isNotEmpty()
			->when(function() use ($adapter) { unset($adapter->{uniqid()}); })
				->array($adapter->getInvokers())->isNotEmpty()
				->array($adapter->getCalls())->isNotEmpty()
			->when(function() use ($adapter) { unset($adapter->md5); })
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter) { $adapter->MD5 = uniqid(); $adapter->MD5(uniqid()); })
				->array($adapter->getInvokers())->isNotEmpty()
				->array($adapter->getCalls())->isNotEmpty()
			->when(function() use ($adapter) { unset($adapter->{uniqid()}); })
				->array($adapter->getInvokers())->isNotEmpty()
				->array($adapter->getCalls())->isNotEmpty()
			->when(function() use ($adapter) { unset($adapter->MD5); })
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
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

	public function testGetCallsNumber()
	{
		$this
			->integer(testedClass::getCallsNumber())->isZero()
			->if($adapter = new testedClass())
			->and($adapter->md5(uniqid()))
			->then
				->integer(testedClass::getCallsNumber())->isEqualTo(1)
			->if($adapter->md5(uniqid()))
			->then
				->integer(testedClass::getCallsNumber())->isEqualTo(2)
			->if($otherAdapter = new testedClass())
			->and($otherAdapter->sha1(uniqid()))
			->then
				->integer(testedClass::getCallsNumber())->isEqualTo(3)
		;
	}

	public function testGetCalls()
	{
		$this
			->if($adapter = new testedClass())
			->then
				->array($adapter->getCalls())->isEmpty()
			->if($adapter->md5($firstHash = uniqid()))
			->then
				->array($adapter->getCalls())->isEqualTo(array('md5' => array(1 => array($firstHash))))
				->array($adapter->getCalls('md5'))->isEqualTo(array(1 => array($firstHash)))
				->array($adapter->getCalls('MD5'))->isEqualTo(array(1 => array($firstHash)))
			->if($adapter->md5($secondHash = uniqid()))
			->then
				->array($adapter->getCalls())->isEqualTo(array('md5' => array(1 => array($firstHash), 2 => array($secondHash))))
				->array($adapter->getCalls('md5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash)))
				->array($adapter->getCalls('MD5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash)))
			->if($adapter->md5 = function() {})
			->and($adapter->md5($thirdHash = uniqid()))
			->then
				->array($adapter->getCalls())->isEqualTo(array('md5' => array(1 => array($firstHash), 2 => array($secondHash), 3 => array($thirdHash))))
				->array($adapter->getCalls('md5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash), 3 => array($thirdHash)))
				->array($adapter->getCalls('MD5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash), 3 => array($thirdHash)))
			->if($adapter->strpos($haystack = uniqid(), $needle = uniqid(), $offset = rand(0, 12)))
			->then
				->array($adapter->getCalls())->isEqualTo(array(
							'md5' => array(
								1 => array($firstHash),
								2 => array($secondHash),
								3 => array($thirdHash)
							),
							'strpos' => array(
								4 => array($haystack, $needle, $offset)
							)
					)
				)
				->array($adapter->getCalls('md5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash), 3 => array($thirdHash)))
				->array($adapter->getCalls('MD5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash), 3 => array($thirdHash)))
				->array($adapter->getCalls('strpos'))->isEqualTo(array(4 => array($haystack, $needle, $offset)))
				->array($adapter->getCalls('STRPOS'))->isEqualTo(array(4 => array($haystack, $needle, $offset)))
		;
	}

	public function testGetTimeline()
	{
		$this
			->if($adapter = new testedClass())
			->then
				->array($adapter->getTimeline())->isEmpty()
			->if($adapter->md5($md5arg1 = uniqid()))
			->then
				->array($adapter->getTimeline())->isEqualTo(array(
						1 => array('md5' => array($md5arg1))
					)
				)
			->if($adapter->md5($md5arg2 = uniqid()))
			->then
				->array($adapter->getTimeline())->isEqualTo(array(
						1 => array('md5' => array($md5arg1)),
						2 => array('md5' => array($md5arg2))
					)
				)
			->if($adapter->sha1($sha1arg1 = uniqid()))
			->then
				->array($adapter->getTimeline())->isEqualTo(array(
						1 => array('md5' => array($md5arg1)),
						2 => array('md5' => array($md5arg2)),
						3 => array('sha1' => array($sha1arg1))
					)
				)
			->if($adapter->md5($md5arg3 = uniqid()))
			->then
				->array($adapter->getTimeline())->isEqualTo(array(
						1 => array('md5' => array($md5arg1)),
						2 => array('md5' => array($md5arg2)),
						3 => array('sha1' => array($sha1arg1)),
						4 => array('md5' => array($md5arg3))
					)
				)
				->array($adapter->getTimeline('md5'))->isEqualTo(array(
						1 => array($md5arg1),
						2 => array($md5arg2),
						4 => array($md5arg3)
					)
				)
				->array($adapter->getTimeline('sha1'))->isEqualTo(array(
						3 => array($sha1arg1)
					)
				)
		;
	}

	public function testAddCall()
	{
		$this
			->if($adapter = new testedClass())
			->then
				->array($adapter->getCalls())->isEmpty()
				->object($adapter->addCall($method = uniqid(), $args = array(uniqid())))->isIdenticalTo($adapter)
				->array($adapter->getCalls($method))->isEqualTo(array(1 => $args))
				->array($adapter->getCalls(strtoupper($method)))->isEqualTo(array(1 => $args))
		;
	}

	public function testResetCalls()
	{
		$this
			->if($adapter = new testedClass())
			->and($adapter->md5(uniqid()))
			->then
				->array($adapter->getCalls())->isNotEmpty()
				->object($adapter->resetCalls())->isIdenticalTo($adapter)
				->array($adapter->getCalls())->isEmpty()
		;
	}

	public function testReset()
	{
		$this
			->if($adapter = new testedClass())
			->then
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
				->object($adapter->reset())->isIdenticalTo($adapter)
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->if($adapter->md5(uniqid()))
			->then
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isNotEmpty()
				->object($adapter->reset())->isIdenticalTo($adapter)
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->if($adapter->md5 = uniqid())
			->then
				->array($adapter->getInvokers())->isNotEmpty()
				->array($adapter->getCalls())->isEmpty()
				->object($adapter->reset())->isIdenticalTo($adapter)
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->if($adapter->md5 = uniqid())
			->and($adapter->md5(uniqid()))
			->then
				->array($adapter->getInvokers())->isNotEmpty()
				->array($adapter->getCalls())->isNotEmpty()
				->object($adapter->reset())->isIdenticalTo($adapter)
				->array($adapter->getInvokers())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
		;
	}
}
