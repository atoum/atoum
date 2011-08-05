<?php

namespace mageekguy\atoum\tests\units\test\adapter;

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter
;

require_once(__DIR__ . '/../../../runner.php');

class caller extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->hasInterface('ArrayAccess')
		;
	}

	public function test__set()
	{
		$caller = new adapter\caller();

		$caller->return = $return = uniqid();

		$this->assert
			->string($caller->invoke())->isEqualTo($return)
		;

		$caller->throw = $exception = new \exception();

		$this->assert
			->exception(function() use ($caller) {
					$caller->invoke();
				}
			)
				->isIdenticalTo($exception)
		;

		$this->assert
			->exception(function() use ($caller) {
					$caller->{uniqid()} = uniqid();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
		;
	}

	public function test__construct()
	{
		$caller = new adapter\caller();

		$this->assert
			->boolean($caller->isEmpty())->isTrue()
			->variable($caller->getCurrentCall())->isNull()
		;
	}

	public function testSetClosure()
	{
		$caller = new adapter\caller();

		$this->assert
			->exception(function() use ($caller) {
					$caller->setClosure(function() {}, - rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->object($caller->setClosure($value = function() {}))->isIdenticalTo($caller)
			->boolean($caller->isEmpty())->isFalse()
			->object($caller->getClosure())->isIdenticalTo($value)
			->object($caller->setClosure($value = function() {}, 0))->isIdenticalTo($caller)
			->boolean($caller->isEmpty())->isFalse()
			->object($caller->getClosure(0))->isIdenticalTo($value)
			->object($caller->setClosure($otherValue = function() {}, $call = rand(2, PHP_INT_MAX)))->isIdenticalTo($caller)
			->boolean($caller->isEmpty())->isFalse()
			->object($caller->getClosure($call))->isIdenticalTo($otherValue)
		;
	}

	public function testGetClosure()
	{
		$caller = new adapter\caller();

		$this->assert
			->exception(function() use ($caller) {
					$caller->getClosure(- rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->variable($caller->getClosure(rand(0, PHP_INT_MAX)))->isNull()
		;

		$caller->setClosure($value = function() {}, 0);

		$this->assert
			->object($caller->getClosure(0))->isIdenticalTo($value)
			->variable($caller->getClosure(1))->isNull()
		;
	}

	public function testClosureIsSet()
	{
		$caller = new adapter\caller();

		$this->assert
			->exception(function() use ($caller) {
					$caller->closureIsSetForCall(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->boolean($caller->closureIsSetForCall(rand(0, PHP_INT_MAX)))->isFalse()
		;

		$caller->setClosure(function() {}, 0);

		$this->assert
			->boolean($caller->closureIsSetForCall())->isTrue()
			->boolean($caller->closureIsSetForCall(0))->isTrue()
			->boolean($caller->closureIsSetForCall(rand(1, PHP_INT_MAX)))->isFalse()
		;
	}

	public function testUnsetClosure()
	{
		$caller = new adapter\caller();

		$this->assert
			->exception(function() use ($caller) {
					$caller->unsetClosure(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->exception(function() use ($caller, & $call) {
					$caller->unsetClosure($call = rand(0, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('There is no closure defined for call ' . $call)
		;

		$caller->setClosure(function() {});

		$this->assert
			->boolean($caller->closureIsSetForCall())->isTrue()
			->object($caller->unsetClosure())->isIdenticalTo($caller)
			->boolean($caller->closureIsSetForCall())->isFalse()
		;
	}

	public function testOffsetSet()
	{
		$caller = new adapter\caller();

		$this->assert
			->exception(function() use ($caller) {
					$caller->offsetSet(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->object($caller->offsetSet(1, $value = function() {}))->isIdenticalTo($caller)
			->boolean($caller->isEmpty())->isFalse()
			->object($caller->getClosure(1))->isIdenticalTo($value)
			->object($caller->offsetSet(2, $mixed = uniqid()))->isIdenticalTo($caller)
			->string($caller->invoke(array(), 2))->isEqualTo($mixed)
		;
	}

	public function testOffsetGet()
	{
		$caller = new adapter\caller();

		$this->assert
			->exception(function() use ($caller) {
					$caller->offsetGet(- rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->variable($caller->getClosure(rand(0, PHP_INT_MAX)))->isNull()
		;

		$caller->setClosure($value = function() {}, 0);

		$this->assert
			->object($caller->offsetGet(0))->isIdenticalTo($caller)
			->variable($caller->getCurrentCall())->isEqualTo(0)
			->object($caller->offsetGet($call = rand(1, PHP_INT_MAX)))->isIdenticalTo($caller)
			->variable($caller->getCurrentCall())->isEqualTo($call)
		;
	}

	public function testOffsetExists()
	{
		$caller = new adapter\caller();

		$this->assert
			->exception(function() use ($caller) {
					$caller->offsetExists(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->boolean($caller->offsetExists(rand(0, PHP_INT_MAX)))->isFalse()
		;

		$caller->setClosure(function() {}, 0);

		$this->assert
			->boolean($caller->offsetExists(0))->isTrue()
			->boolean($caller->offsetExists(rand(1, PHP_INT_MAX)))->isFalse()
		;
	}

	public function testOffsetUnset()
	{
		$caller = new adapter\caller();

		$this->assert
			->exception(function() use ($caller) {
					$caller->offsetUnset(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->exception(function() use ($caller, & $call) {
					$caller->offsetUnset($call = rand(0, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('There is no closure defined for call ' . $call)
		;

		$caller->setClosure(function() {});

		$this->assert
			->boolean($caller->closureIsSetForCall(0))->isTrue()
			->object($caller->offsetUnset(0))->isIdenticalTo($caller)
			->boolean($caller->closureIsSetForCall(0))->isFalse()
		;
	}

	public function testInvoke()
	{
		$caller = new adapter\caller();

		$this->assert
			->exception(function() use ($caller) {
					$caller->invoke();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('There is no closure defined for call 0')
		;

		$caller->setClosure(function($string) { return md5($string); });
		$caller->setClosure(function() use (& $md5) { return $md5 = uniqid(); }, 1);
		$caller->setClosure(function() use (& $md5) { return $md5 = uniqid(); }, $call = rand(2, PHP_INT_MAX));

		$this->assert
			->string($caller->invoke(array($string = uniqid())))->isEqualTo(md5($string))
			->string($caller->invoke(array($string = uniqid()), 0))->isEqualTo(md5($string))
			->string($caller->invoke(array($string = uniqid()), 1))->isEqualTo($md5)
			->string($caller->invoke(array($string = uniqid())))->isEqualTo(md5($string))
			->string($caller->invoke(array($string = uniqid()), 0))->isEqualTo(md5($string))
			->string($caller->invoke(array($string = uniqid()), $call))->isEqualTo($md5)
		;
	}

	public function testAtCall()
	{
		$caller = new adapter\caller();

		$defaultReturn = uniqid();
		$caller->setClosure(function () use ($defaultReturn) { return $defaultReturn; }, 0);

		$this->assert
			->variable($caller->getCurrentCall())->isNull()
			->object($caller->atCall($call = rand(1, PHP_INT_MAX)))->isIdenticalTo($caller)
			->integer($caller->getCurrentCall())->isEqualTo($call)
		;
	}
}

?>
