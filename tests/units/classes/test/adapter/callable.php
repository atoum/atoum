<?php

namespace mageekguy\atoum\tests\units\test\adapter;

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter
;

require_once __DIR__ . '/../../../runner.php';

class invoker extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->hasInterface('ArrayAccess')
		;
	}

	public function test__set()
	{
		$invoker = new adapter\invoker();

		$invoker->return = $return = uniqid();

		$this->assert
			->string($invoker->invoke())->isEqualTo($return)
		;

		$invoker->throw = $exception = new \exception();

		$this->assert
			->exception(function() use ($invoker) {
					$invoker->invoke();
				}
			)
				->isIdenticalTo($exception)
		;

		$this->assert
			->exception(function() use ($invoker) {
					$invoker->{uniqid()} = uniqid();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
		;
	}

	public function test__construct()
	{
		$invoker = new adapter\invoker();

		$this->assert
			->boolean($invoker->isEmpty())->isTrue()
			->variable($invoker->getCurrentCall())->isNull()
		;
	}

	public function testSetClosure()
	{
		$invoker = new adapter\invoker();

		$this->assert
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
			->object($invoker->setClosure($otherValue = function() {}, $call = rand(2, PHP_INT_MAX)))->isIdenticalTo($invoker)
			->boolean($invoker->isEmpty())->isFalse()
			->object($invoker->getClosure($call))->isIdenticalTo($otherValue)
		;
	}

	public function testGetClosure()
	{
		$invoker = new adapter\invoker();

		$this->assert
			->exception(function() use ($invoker) {
					$invoker->getClosure(- rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->variable($invoker->getClosure(rand(0, PHP_INT_MAX)))->isNull()
		;

		$invoker->setClosure($value = function() {}, 0);

		$this->assert
			->object($invoker->getClosure(0))->isIdenticalTo($value)
			->variable($invoker->getClosure(1))->isNull()
		;
	}

	public function testClosureIsSet()
	{
		$invoker = new adapter\invoker();

		$this->assert
			->exception(function() use ($invoker) {
					$invoker->closureIsSetForCall(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->boolean($invoker->closureIsSetForCall(rand(0, PHP_INT_MAX)))->isFalse()
		;

		$invoker->setClosure(function() {}, 0);

		$this->assert
			->boolean($invoker->closureIsSetForCall())->isTrue()
			->boolean($invoker->closureIsSetForCall(0))->isTrue()
			->boolean($invoker->closureIsSetForCall(rand(1, PHP_INT_MAX)))->isFalse()
		;
	}

	public function testUnsetClosure()
	{
		$invoker = new adapter\invoker();

		$this->assert
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
		;

		$invoker->setClosure(function() {});

		$this->assert
			->boolean($invoker->closureIsSetForCall())->isTrue()
			->object($invoker->unsetClosure())->isIdenticalTo($invoker)
			->boolean($invoker->closureIsSetForCall())->isFalse()
		;
	}

	public function testOffsetSet()
	{
		$invoker = new adapter\invoker();

		$this->assert
			->exception(function() use ($invoker) {
					$invoker->offsetSet(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->object($invoker->offsetSet(1, $value = function() {}))->isIdenticalTo($invoker)
			->boolean($invoker->isEmpty())->isFalse()
			->object($invoker->getClosure(1))->isIdenticalTo($value)
			->object($invoker->offsetSet(2, $mixed = uniqid()))->isIdenticalTo($invoker)
			->string($invoker->invoke(array(), 2))->isEqualTo($mixed)
		;
	}

	public function testOffsetGet()
	{
		$invoker = new adapter\invoker();

		$this->assert
			->exception(function() use ($invoker) {
					$invoker->offsetGet(- rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->variable($invoker->getClosure(rand(0, PHP_INT_MAX)))->isNull()
		;

		$invoker->setClosure($value = function() {}, 0);

		$this->assert
			->object($invoker->offsetGet(0))->isIdenticalTo($invoker)
			->variable($invoker->getCurrentCall())->isEqualTo(0)
			->object($invoker->offsetGet($call = rand(1, PHP_INT_MAX)))->isIdenticalTo($invoker)
			->variable($invoker->getCurrentCall())->isEqualTo($call)
		;
	}

	public function testOffsetExists()
	{
		$invoker = new adapter\invoker();

		$this->assert
			->exception(function() use ($invoker) {
					$invoker->offsetExists(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->boolean($invoker->offsetExists(rand(0, PHP_INT_MAX)))->isFalse()
		;

		$invoker->setClosure(function() {}, 0);

		$this->assert
			->boolean($invoker->offsetExists(0))->isTrue()
			->boolean($invoker->offsetExists(rand(1, PHP_INT_MAX)))->isFalse()
		;
	}

	public function testOffsetUnset()
	{
		$invoker = new adapter\invoker();

		$this->assert
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
		;

		$invoker->setClosure(function() {});

		$this->assert
			->boolean($invoker->closureIsSetForCall(0))->isTrue()
			->object($invoker->offsetUnset(0))->isIdenticalTo($invoker)
			->boolean($invoker->closureIsSetForCall(0))->isFalse()
		;
	}

	public function testInvoke()
	{
		$invoker = new adapter\invoker();

		$this->assert
			->exception(function() use ($invoker) {
					$invoker->invoke();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('There is no closure defined for call 0')
		;

		$invoker->setClosure(function($string) { return md5($string); });
		$invoker->setClosure(function() use (& $md5) { return $md5 = uniqid(); }, 1);
		$invoker->setClosure(function() use (& $md5) { return $md5 = uniqid(); }, $call = rand(2, PHP_INT_MAX));

		$this->assert
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
		$invoker = new adapter\invoker();

		$defaultReturn = uniqid();
		$invoker->setClosure(function () use ($defaultReturn) { return $defaultReturn; }, 0);

		$this->assert
			->variable($invoker->getCurrentCall())->isNull()
			->object($invoker->atCall($call = rand(1, PHP_INT_MAX)))->isIdenticalTo($invoker)
			->integer($invoker->getCurrentCall())->isEqualTo($call)
		;
	}
}
