<?php

namespace mageekguy\atoum\tests\units\test\adapter;

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter
;

require_once(__DIR__ . '/../../../runner.php');

class callable extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->hasInterface('ArrayAccess')
		;
	}

	public function test__set()
	{
		$callable = new adapter\callable();

		$callable->return = $return = uniqid();

		$this->assert
			->string($callable->invoke())->isEqualTo($return)
		;

		$callable->throw = $exception = new \exception();

		$this->assert
			->exception(function() use ($callable) {
					$callable->invoke();
				}
			)
				->isIdenticalTo($exception)
		;

		$this->assert
			->exception(function() use ($callable) {
					$callable->{uniqid()} = uniqid();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
		;
	}

	public function test__construct()
	{
		$callable = new adapter\callable();

		$this->assert
			->boolean($callable->isEmpty())->isTrue()
			->variable($callable->getCurrentCall())->isNull()
		;
	}

	public function testSetClosure()
	{
		$callable = new adapter\callable();

		$this->assert
			->exception(function() use ($callable) {
					$callable->setClosure(function() {}, - rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->object($callable->setClosure($value = function() {}))->isIdenticalTo($callable)
			->boolean($callable->isEmpty())->isFalse()
			->object($callable->getClosure())->isIdenticalTo($value)
			->object($callable->setClosure($value = function() {}, 0))->isIdenticalTo($callable)
			->boolean($callable->isEmpty())->isFalse()
			->object($callable->getClosure(0))->isIdenticalTo($value)
			->object($callable->setClosure($otherValue = function() {}, $call = rand(2, PHP_INT_MAX)))->isIdenticalTo($callable)
			->boolean($callable->isEmpty())->isFalse()
			->object($callable->getClosure($call))->isIdenticalTo($otherValue)
		;
	}

	public function testGetClosure()
	{
		$callable = new adapter\callable();

		$this->assert
			->exception(function() use ($callable) {
					$callable->getClosure(- rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->variable($callable->getClosure(rand(0, PHP_INT_MAX)))->isNull()
		;

		$callable->setClosure($value = function() {}, 0);

		$this->assert
			->object($callable->getClosure(0))->isIdenticalTo($value)
			->variable($callable->getClosure(1))->isNull()
		;
	}

	public function testClosureIsSet()
	{
		$callable = new adapter\callable();

		$this->assert
			->exception(function() use ($callable) {
					$callable->closureIsSetForCall(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->boolean($callable->closureIsSetForCall(rand(0, PHP_INT_MAX)))->isFalse()
		;

		$callable->setClosure(function() {}, 0);

		$this->assert
			->boolean($callable->closureIsSetForCall())->isTrue()
			->boolean($callable->closureIsSetForCall(0))->isTrue()
			->boolean($callable->closureIsSetForCall(rand(1, PHP_INT_MAX)))->isFalse()
		;
	}

	public function testUnsetClosure()
	{
		$callable = new adapter\callable();

		$this->assert
			->exception(function() use ($callable) {
					$callable->unsetClosure(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->exception(function() use ($callable, & $call) {
					$callable->unsetClosure($call = rand(0, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('There is no closure defined for call ' . $call)
		;

		$callable->setClosure(function() {});

		$this->assert
			->boolean($callable->closureIsSetForCall())->isTrue()
			->object($callable->unsetClosure())->isIdenticalTo($callable)
			->boolean($callable->closureIsSetForCall())->isFalse()
		;
	}

	public function testOffsetSet()
	{
		$callable = new adapter\callable();

		$this->assert
			->exception(function() use ($callable) {
					$callable->offsetSet(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->object($callable->offsetSet(1, $value = function() {}))->isIdenticalTo($callable)
			->boolean($callable->isEmpty())->isFalse()
			->object($callable->getClosure(1))->isIdenticalTo($value)
			->object($callable->offsetSet(2, $mixed = uniqid()))->isIdenticalTo($callable)
			->string($callable->invoke(array(), 2))->isEqualTo($mixed)
		;
	}

	public function testOffsetGet()
	{
		$callable = new adapter\callable();

		$this->assert
			->exception(function() use ($callable) {
					$callable->offsetGet(- rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->variable($callable->getClosure(rand(0, PHP_INT_MAX)))->isNull()
		;

		$callable->setClosure($value = function() {}, 0);

		$this->assert
			->object($callable->offsetGet(0))->isIdenticalTo($callable)
			->variable($callable->getCurrentCall())->isEqualTo(0)
			->object($callable->offsetGet($call = rand(1, PHP_INT_MAX)))->isIdenticalTo($callable)
			->variable($callable->getCurrentCall())->isEqualTo($call)
		;
	}

	public function testOffsetExists()
	{
		$callable = new adapter\callable();

		$this->assert
			->exception(function() use ($callable) {
					$callable->offsetExists(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->boolean($callable->offsetExists(rand(0, PHP_INT_MAX)))->isFalse()
		;

		$callable->setClosure(function() {}, 0);

		$this->assert
			->boolean($callable->offsetExists(0))->isTrue()
			->boolean($callable->offsetExists(rand(1, PHP_INT_MAX)))->isFalse()
		;
	}

	public function testOffsetUnset()
	{
		$callable = new adapter\callable();

		$this->assert
			->exception(function() use ($callable) {
					$callable->offsetUnset(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->exception(function() use ($callable, & $call) {
					$callable->offsetUnset($call = rand(0, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('There is no closure defined for call ' . $call)
		;

		$callable->setClosure(function() {});

		$this->assert
			->boolean($callable->closureIsSetForCall(0))->isTrue()
			->object($callable->offsetUnset(0))->isIdenticalTo($callable)
			->boolean($callable->closureIsSetForCall(0))->isFalse()
		;
	}

	public function testInvoke()
	{
		$callable = new adapter\callable();

		$this->assert
			->exception(function() use ($callable) {
					$callable->invoke();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('There is no closure defined for call 0')
		;

		$callable->setClosure(function($string) { return md5($string); });
		$callable->setClosure(function() use (& $md5) { return $md5 = uniqid(); }, 1);
		$callable->setClosure(function() use (& $md5) { return $md5 = uniqid(); }, $call = rand(2, PHP_INT_MAX));

		$this->assert
			->string($callable->invoke(array($string = uniqid())))->isEqualTo(md5($string))
			->string($callable->invoke(array($string = uniqid()), 0))->isEqualTo(md5($string))
			->string($callable->invoke(array($string = uniqid()), 1))->isEqualTo($md5)
			->string($callable->invoke(array($string = uniqid())))->isEqualTo(md5($string))
			->string($callable->invoke(array($string = uniqid()), 0))->isEqualTo(md5($string))
			->string($callable->invoke(array($string = uniqid()), $call))->isEqualTo($md5)
		;
	}

	public function testAtCall()
	{
		$callable = new adapter\callable();

		$defaultReturn = uniqid();
		$callable->setClosure(function () use ($defaultReturn) { return $defaultReturn; }, 0);

		$this->assert
			->variable($callable->getCurrentCall())->isNull()
			->object($callable->atCall($call = rand(1, PHP_INT_MAX)))->isIdenticalTo($callable)
			->integer($callable->getCurrentCall())->isEqualTo($call)
		;
	}
}

?>
