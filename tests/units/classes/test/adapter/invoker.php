<?php

namespace mageekguy\atoum\tests\units\test\adapter;

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter\invoker as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class invoker extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->implements('arrayAccess')
			->implements('countable')
		;
	}

	public function test__set()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->and($invoker->return = $return = uniqid())
			->then
				->string($invoker->invoke())->isEqualTo($return)
			->if($invoker->throw = $exception = new \exception())
			->then
				->exception(function() use ($invoker) {
						$invoker->invoke();
					}
				)
					->isIdenticalTo($exception)
			->if($invoker = new testedClass(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->{uniqid()} = uniqid();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
		;
	}

	public function test__construct()
	{
		$this
			->if($invoker = new testedClass($function = uniqid()))
			->then
				->string($invoker->getFunction())->isEqualTo($function)
				->boolean($invoker->isEmpty())->isTrue()
				->variable($invoker->getCurrentCall())->isNull()
		;
	}

	public function testDoesNothing()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->then
				->object($invoker->doesNothing())->isIdenticalTo($invoker)
				->boolean($invoker->closureIsSetForCall(0))->isTrue()
				->variable($invoker->invoke())->isNull()
		;
	}

	public function testDoesSomething()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->and($invoker->doesNothing())
			->then
				->object($invoker->doesSomething())->isIdenticalTo($invoker)
				->boolean($invoker->closureIsSetForCall(0))->isFalse()
		;
	}

	public function testCount()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->then
				->sizeof($invoker)->isZero()
			->if($invoker->setClosure(function() {}))
			->then
				->sizeof($invoker)->isEqualTo(1)
			->if($invoker->doesNothing())
			->then
				->sizeof($invoker)->isEqualTo(1)
			->if($invoker->setClosure(function() {}, 1))
			->then
				->sizeof($invoker)->isEqualTo(2)
		;
	}

	public function testSetClosure()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->setClosure(function() {}, - rand(1, PHP_INT_MAX));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
				->object($invoker->setClosure($value = function() {}))->isIdenticalTo($invoker)
				->boolean($invoker->isEmpty())->isFalse()
				->object($invoker->getClosure())->isIdenticalTo($value)
				->object($invoker->setClosure($value = function() {}, 0))->isIdenticalTo($invoker)
				->boolean($invoker->isEmpty())->isFalse()
				->object($invoker->getClosure(0))->isIdenticalTo($value)
				->object($invoker->setClosure($otherValue = function() {}, $call = rand(2, PHP_INT_MAX - 1)))->isIdenticalTo($invoker)
				->boolean($invoker->isEmpty())->isFalse()
				->object($invoker->getClosure($call))->isIdenticalTo($otherValue)
				->object($invoker->setClosure($nextValue = function() {}, null))->isIdenticalTo($invoker)
				->boolean($invoker->isEmpty())->isFalse()
				->object($invoker->getClosure($call + 1))->isIdenticalTo($nextValue)
		;
	}

	public function testGetClosure()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->getClosure(- rand(1, PHP_INT_MAX));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
				->variable($invoker->getClosure(rand(0, PHP_INT_MAX)))->isNull()
			->if($invoker->setClosure($value = function() {}, 0))
			->then
				->object($invoker->getClosure(0))->isIdenticalTo($value)
				->object($invoker->getClosure(1))->isIdenticalTo($value)
				->object($invoker->getClosure(rand(2, PHP_INT_MAX)))->isIdenticalTo($value)
			->if($invoker->unsetClosure(0))
			->then
				->variable($invoker->getClosure(0))->isNull()
				->variable($invoker->getClosure(1))->isNull()
				->variable($invoker->getClosure(rand(2, PHP_INT_MAX)))->isNull()
			->if($invoker->setClosure($value = function() {}, $call = rand(2, PHP_INT_MAX - 1)))
			->then
				->variable($invoker->getClosure(0))->isNull()
				->variable($invoker->getClosure($call - 1))->isNull()
				->object($invoker->getClosure($call))->isIdenticalTo($value)
				->variable($invoker->getClosure($call + 1))->isNull()
		;
	}

	public function testClosureIsSet()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->closureIsSetForCall(- rand(1, PHP_INT_MAX), function() {});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
				->boolean($invoker->closureIsSetForCall(rand(0, PHP_INT_MAX)))->isFalse()
			->if($invoker->setClosure(function() {}, 0))
			->then
				->boolean($invoker->closureIsSetForCall())->isTrue()
				->boolean($invoker->closureIsSetForCall(0))->isTrue()
				->boolean($invoker->closureIsSetForCall(rand(1, PHP_INT_MAX)))->isTrue()
			->if($invoker->setClosure(function() {}, $call = rand(2, PHP_INT_MAX - 1)))
			->and($invoker->unsetClosure(0))
			->then
				->boolean($invoker->closureIsSetForCall())->isFalse()
				->boolean($invoker->closureIsSetForCall(0))->isFalse()
				->boolean($invoker->closureIsSetForCall($call - 1))->isFalse()
				->boolean($invoker->closureIsSetForCall($call))->isTrue()
				->boolean($invoker->closureIsSetForCall($call + 1))->isFalse()
		;
	}

	public function testUnsetClosure()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->unsetClosure(- rand(1, PHP_INT_MAX), function() {});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
				->exception(function() use ($invoker, & $call) {
						$invoker->unsetClosure($call = rand(0, PHP_INT_MAX), function() {});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('There is no closure defined for call ' . $call)
			->if($invoker->setClosure(function() {}))
			->then
				->boolean($invoker->closureIsSetForCall())->isTrue()
				->object($invoker->unsetClosure())->isIdenticalTo($invoker)
				->boolean($invoker->closureIsSetForCall())->isFalse()
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->offsetSet(- rand(1, PHP_INT_MAX), function() {});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
			->if($invoker[1] = $value = function() {})
			->then
				->boolean($invoker->isEmpty())->isFalse()
				->object($invoker->getClosure(1))->isIdenticalTo($value)
			->if($invoker[2] = $mixed = uniqid())
			->then
				->string($invoker->invoke(array(), 2))->isEqualTo($mixed)
			->if($invoker[] = $otherMixed = uniqid())
			->then
				->string($invoker->invoke(array(), 3))->isEqualTo($otherMixed)
			->if($invoker[5] = uniqid())
			->and($invoker[] = $lastMixed = uniqid())
			->then
				->boolean(isset($invoker[4]))->isFalse()
				->boolean(isset($invoker[5]))->isTrue()
				->boolean(isset($invoker[6]))->isTrue()
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->offsetGet(- rand(1, PHP_INT_MAX));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
				->variable($invoker->getClosure(rand(0, PHP_INT_MAX)))->isNull()
			->if($invoker->setClosure($value = function() {}, 0))
			->then
				->object($invoker->offsetGet(0))->isIdenticalTo($invoker)
				->variable($invoker->getCurrentCall())->isEqualTo(0)
				->object($invoker->offsetGet($call = rand(1, PHP_INT_MAX)))->isIdenticalTo($invoker)
				->variable($invoker->getCurrentCall())->isEqualTo($call)
		;
	}

	public function testOffsetExists()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->offsetExists(- rand(1, PHP_INT_MAX), function() {});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
				->boolean($invoker->offsetExists(rand(0, PHP_INT_MAX)))->isFalse()
			->if($invoker->setClosure(function() {}, 0))
			->then
				->boolean($invoker->offsetExists(0))->isTrue()
				->boolean($invoker->offsetExists(rand(1, PHP_INT_MAX)))->isTrue()
			->if($invoker = new testedClass(uniqid()))
			->and($invoker->setClosure(function() {}, 2))
			->then
				->boolean($invoker->offsetExists(0))->isFalse()
				->boolean($invoker->offsetExists(1))->isFalse()
				->boolean($invoker->offsetExists(2))->isTrue()
				->boolean($invoker->offsetExists(3))->isFalse()
			->if($invoker->setClosure(function() {}, 0))
				->boolean($invoker->offsetExists(0))->isTrue()
				->boolean($invoker->offsetExists(1))->isTrue()
				->boolean($invoker->offsetExists(2))->isTrue()
				->boolean($invoker->offsetExists(3))->isTrue()
		;
	}

	public function testOffsetUnset()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->offsetUnset(- rand(1, PHP_INT_MAX), function() {});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
				->exception(function() use ($invoker, & $call) {
						$invoker->offsetUnset($call = rand(0, PHP_INT_MAX), function() {});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('There is no closure defined for call ' . $call)
			->if($invoker->setClosure(function() {}))
			->then
				->boolean($invoker->closureIsSetForCall(0))->isTrue()
				->object($invoker->offsetUnset(0))->isIdenticalTo($invoker)
				->boolean($invoker->closureIsSetForCall(0))->isFalse()
		;
	}

	public function testInvoke()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->invoke();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('There is no closure defined for call 0')
			->if($invoker->setClosure(function($string) { return md5($string); }))
			->and($invoker->setClosure(function() use (& $md5) { return $md5 = uniqid(); }, 1))
			->and($invoker->setClosure(function() use (& $md5) { return $md5 = uniqid(); }, $call = rand(2, PHP_INT_MAX)))
			->then
				->string($invoker->invoke(array($string = uniqid())))->isEqualTo(md5($string))
				->string($invoker->invoke(array($string = uniqid()), 0))->isEqualTo(md5($string))
				->string($invoker->invoke(array($string = uniqid()), 1))->isEqualTo($md5)
				->string($invoker->invoke(array($string = uniqid())))->isEqualTo(md5($string))
				->string($invoker->invoke(array($string = uniqid()), 0))->isEqualTo(md5($string))
				->string($invoker->invoke(array($string = uniqid()), $call))->isEqualTo($md5)
		;
	}

	public function testAtCall()
	{
		$this
			->if($invoker = new testedClass(uniqid()))
			->and($invoker->setClosure(function () use (& $defaultReturn) { return $defaultReturn = uniqid(); }, 0))
			->then
				->variable($invoker->getCurrentCall())->isNull()
				->object($invoker->atCall($call = rand(1, PHP_INT_MAX)))->isIdenticalTo($invoker)
				->integer($invoker->getCurrentCall())->isEqualTo($call)
		;
	}
}
