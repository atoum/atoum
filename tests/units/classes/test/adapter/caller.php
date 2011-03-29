<?php

namespace mageekguy\atoum\tests\units\test\adapter;

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter
;

require_once(__DIR__ . '/../../runner.php');

class caller extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->hasInterface('\ArrayAccess')
		;
	}

	public function test__construct()
	{
		$closure = new adapter\caller();

		$this->assert
			->boolean($closure->isEmpty())->isTrue()
		;
	}

	public function testSetClosure()
	{
		$closure = new adapter\caller();

		$this->assert
			->exception(function() use ($closure) {
					$closure->setClosure(function() {}, - rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->object($closure->setClosure($value = function() {}))->isIdenticalTo($closure)
			->boolean($closure->isEmpty())->isFalse()
			->object($closure->getClosure())->isIdenticalTo($value)
			->object($closure->setClosure($value = function() {}, 0))->isIdenticalTo($closure)
			->boolean($closure->isEmpty())->isFalse()
			->object($closure->getClosure(0))->isIdenticalTo($value)
			->object($closure->setClosure($otherValue = function() {}, $call = rand(2, PHP_INT_MAX)))->isIdenticalTo($closure)
			->boolean($closure->isEmpty())->isFalse()
			->object($closure->getClosure($call))->isIdenticalTo($otherValue)
		;
	}

	public function testGetClosure()
	{
		$closure = new adapter\caller();

		$this->assert
			->exception(function() use ($closure) {
					$closure->getClosure(- rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->variable($closure->getClosure(rand(0, PHP_INT_MAX)))->isNull()
		;

		$closure->setClosure($value = function() {}, 0);

		$this->assert
			->object($closure->getClosure(0))->isIdenticalTo($value)
			->variable($closure->getClosure(1))->isNull()
		;
	}

	public function testClosureIsSet()
	{
		$closure = new adapter\caller();

		$this->assert
			->exception(function() use ($closure) {
					$closure->closureIsSet(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->boolean($closure->closureIsSet(rand(0, PHP_INT_MAX)))->isFalse()
		;

		$closure->setClosure(function() {}, 0);

		$this->assert
			->boolean($closure->closureIsSet())->isTrue()
			->boolean($closure->closureIsSet(0))->isTrue()
			->boolean($closure->closureIsSet(rand(1, PHP_INT_MAX)))->isFalse()
		;
	}

	public function testUnsetClosure()
	{
		$closure = new adapter\caller();

		$this->assert
			->exception(function() use ($closure) {
					$closure->unsetClosure(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->exception(function() use ($closure, & $call) {
					$closure->unsetClosure($call = rand(0, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('There is no closure defined for call ' . $call)
		;

		$closure->setClosure(function() {});

		$this->assert
			->boolean($closure->closureIsSet())->isTrue()
			->object($closure->unsetClosure())->isIdenticalTo($closure)
			->boolean($closure->closureIsSet())->isFalse()
		;
	}

	public function testOffsetSet()
	{
		$closure = new adapter\caller();

		$this->assert
			->exception(function() use ($closure) {
					$closure->offsetSet(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->object($closure->offsetSet(1, $value = function() {}))->isIdenticalTo($closure)
			->boolean($closure->isEmpty())->isFalse()
			->object($closure->getClosure(1))->isIdenticalTo($value)
			->object($closure->offsetSet(2, $mixed = uniqid()))->isIdenticalTo($closure)
			->string($closure->invoke(array(), 2))->isEqualTo($mixed)
		;
	}

	public function testOffsetGet()
	{
		$closure = new adapter\caller();

		$this->assert
			->exception(function() use ($closure) {
					$closure->offsetGet(- rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->variable($closure->getClosure(rand(0, PHP_INT_MAX)))->isNull()
		;

		$closure->setClosure($value = function() {}, 0);

		$this->assert
			->object($closure->offsetGet(0))->isIdenticalTo($value)
			->variable($closure->offsetGet(1))->isNull()
		;
	}

	public function testOffsetExists()
	{
		$closure = new adapter\caller();

		$this->assert
			->exception(function() use ($closure) {
					$closure->offsetExists(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->boolean($closure->offsetExists(rand(0, PHP_INT_MAX)))->isFalse()
		;

		$closure->setClosure(function() {}, 0);

		$this->assert
			->boolean($closure->offsetExists(0))->isTrue()
			->boolean($closure->offsetExists(rand(1, PHP_INT_MAX)))->isFalse()
		;
	}

	public function testOffsetUnset()
	{
		$closure = new adapter\caller();

		$this->assert
			->exception(function() use ($closure) {
					$closure->offsetUnset(- rand(1, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to zero')
			->exception(function() use ($closure, & $call) {
					$closure->offsetUnset($call = rand(0, PHP_INT_MAX), function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('There is no closure defined for call ' . $call)
		;

		$closure->setClosure(function() {});

		$this->assert
			->boolean($closure->closureIsSet(0))->isTrue()
			->object($closure->offsetUnset(0))->isIdenticalTo($closure)
			->boolean($closure->closureIsSet(0))->isFalse()
		;
	}

	public function testInvoke()
	{
		$closure = new adapter\caller();

		$this->assert
			->exception(function() use ($closure) {
					$closure->invoke();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('There is no closure defined for call 0')
		;

		$closure->setClosure(function($string) { return md5($string); });
		$closure->setClosure(function() use (& $md5) { return $md5 = uniqid(); }, 1);

		$this->assert
			->string($closure->invoke(array($string = uniqid())))->isEqualTo(md5($string))
			->string($closure->invoke(array($string = uniqid()), 0))->isEqualTo(md5($string))
			->string($closure->invoke(array($string = uniqid()), 1))->isEqualTo($md5)
			->string($closure->invoke(array($string = uniqid())))->isEqualTo(md5($string))
			->string($closure->invoke(array($string = uniqid()), 0))->isEqualTo(md5($string))
		;
	}
}

?>
