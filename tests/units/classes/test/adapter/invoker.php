<?php

namespace mageekguy\atoum\tests\units\test\adapter;

use
	mageekguy\atoum
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

	public function test__construct()
	{
		$this
			->given($this->newTestedInstance($function = uniqid()))
			->then
				->string($this->testedInstance->getFunction())->isEqualTo($function)
				->boolean($this->testedInstance->isEmpty())->isTrue
				->variable($this->testedInstance->getCurrentCall())->isNull()
		;
	}

	public function test__set()
	{
		$this
			->given($invoker = $this->newTestedInstance(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->{uniqid()} = uniqid();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')

			->if($this->testedInstance->return = $return = uniqid())
			->then
				->string($this->testedInstance->invoke())->isEqualTo($return)

			->if($this->testedInstance->throw = $exception = new \exception())
			->then
				->exception(function() use ($invoker) {
						$invoker->invoke();
					}
				)
					->isIdenticalTo($exception)
		;
	}

	public function testDoesNothing()
	{
		$this
			->given($this->newTestedInstance(uniqid()))
			->then
				->object($this->testedInstance->doesNothing())->isTestedInstance
				->boolean($this->testedInstance->closureIsSetForCall(0))->isTrue
				->variable($this->testedInstance->invoke())->isNull()

			->given($this->newTestedInstance(uniqid()))
			->then
				->object($this->testedInstance->doesNothing)->isTestedInstance
				->boolean($this->testedInstance->closureIsSetForCall(0))->isTrue
				->variable($this->testedInstance->invoke())->isNull()
		;
	}

	public function testDoesSomething()
	{
		$this
			->given($this->newTestedInstance(uniqid()))
			->then
				->if($this->testedInstance->doesNothing)
				->then
					->object($this->testedInstance->doesSomething())->isTestedInstance
					->boolean($this->testedInstance->closureIsSetForCall(0))->isFalse

				->if($this->testedInstance->doesNothing)
				->then
					->object($this->testedInstance->doesSomething)->isTestedInstance
					->boolean($this->testedInstance->closureIsSetForCall(0))->isFalse
		;
	}

	public function testCount()
	{
		$this
			->given($this->newTestedInstance(uniqid()))
			->then
				->sizeof($this->testedInstance)->isZero()

			->if($this->testedInstance->setClosure(function() {}))
			->then
				->sizeof($this->testedInstance)->isEqualTo(1)

			->if($this->testedInstance->doesNothing())
			->then
				->sizeof($this->testedInstance)->isEqualTo(1)

			->if($this->testedInstance->setClosure(function() {}, 1))
			->then
				->sizeof($this->testedInstance)->isEqualTo(2)
		;
	}

	public function testSetClosure()
	{
		$this
			->given($invoker = $this->newTestedInstance(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->setClosure(function() {}, - rand(1, PHP_INT_MAX));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')

				->object($this->testedInstance->setClosure($value = function() {}))->isTestedInstance
				->boolean($this->testedInstance->isEmpty())->isFalse
				->object($this->testedInstance->getClosure())->isIdenticalTo($value)

				->object($this->testedInstance->setClosure($value = function() {}, 0))->isTestedInstance
				->boolean($this->testedInstance->isEmpty())->isFalse
				->object($this->testedInstance->getClosure(0))->isIdenticalTo($value)

				->object($this->testedInstance->setClosure($otherValue = function() {}, $call = rand(2, PHP_INT_MAX - 1)))->isTestedInstance
				->boolean($this->testedInstance->isEmpty())->isFalse
				->object($this->testedInstance->getClosure($call))->isIdenticalTo($otherValue)

				->object($this->testedInstance->setClosure($nextValue = function() {}, null))->isTestedInstance
				->boolean($this->testedInstance->isEmpty())->isFalse
				->object($this->testedInstance->getClosure($call + 1))->isIdenticalTo($nextValue)
		;
	}

	public function testGetClosure()
	{
		$this
			->given($invoker = $this->newTestedInstance(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->getClosure(- rand(1, PHP_INT_MAX));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
				->variable($this->testedInstance->getClosure(rand(0, PHP_INT_MAX)))->isNull()

			->if($this->testedInstance->setClosure($value = function() {}, 0))
			->then
				->object($this->testedInstance->getClosure(0))->isIdenticalTo($value)
				->object($this->testedInstance->getClosure(1))->isIdenticalTo($value)
				->object($this->testedInstance->getClosure(rand(2, PHP_INT_MAX)))->isIdenticalTo($value)

			->if($this->testedInstance->unsetClosure(0))
			->then
				->variable($this->testedInstance->getClosure(0))->isNull()
				->variable($this->testedInstance->getClosure(1))->isNull()
				->variable($this->testedInstance->getClosure(rand(2, PHP_INT_MAX)))->isNull()

			->if($this->testedInstance->setClosure($value = function() {}, $call = rand(2, PHP_INT_MAX - 1)))
			->then
				->variable($this->testedInstance->getClosure(0))->isNull()
				->variable($this->testedInstance->getClosure($call - 1))->isNull()
				->object($this->testedInstance->getClosure($call))->isIdenticalTo($value)
				->variable($this->testedInstance->getClosure($call + 1))->isNull()
		;
	}

	public function testClosureIsSet()
	{
		$this
			->given($invoker = $this->newTestedInstance(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->closureIsSetForCall(- rand(1, PHP_INT_MAX), function() {});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
				->boolean($this->testedInstance->closureIsSetForCall(rand(0, PHP_INT_MAX)))->isFalse

			->if($this->testedInstance->setClosure(function() {}, 0))
			->then
				->boolean($this->testedInstance->closureIsSetForCall())->isTrue
				->boolean($this->testedInstance->closureIsSetForCall(0))->isTrue
				->boolean($this->testedInstance->closureIsSetForCall(rand(1, PHP_INT_MAX)))->isTrue

			->if(
				$this->testedInstance->setClosure(function() {}, $call = rand(2, PHP_INT_MAX - 1)),
				$this->testedInstance->unsetClosure(0)
			)
			->then
				->boolean($this->testedInstance->closureIsSetForCall())->isFalse
				->boolean($this->testedInstance->closureIsSetForCall(0))->isFalse
				->boolean($this->testedInstance->closureIsSetForCall($call - 1))->isFalse
				->boolean($this->testedInstance->closureIsSetForCall($call))->isTrue
				->boolean($this->testedInstance->closureIsSetForCall($call + 1))->isFalse
		;
	}

	public function testUnsetClosure()
	{
		$this
			->given($invoker = $this->newTestedInstance(uniqid()))
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

			->if($this->testedInstance->setClosure(function() {}))
			->then
				->object($this->testedInstance->unsetClosure())->isTestedInstance
				->boolean($this->testedInstance->closureIsSetForCall())->isFalse
		;
	}

	public function testOffsetSet()
	{
		$this
			->given($invoker = $this->newTestedInstance(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->offsetSet(- rand(1, PHP_INT_MAX), function() {});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')

			->if($this->testedInstance[1] = $value = function() {})
			->then
				->boolean($this->testedInstance->isEmpty())->isFalse
				->object($this->testedInstance->getClosure(1))->isIdenticalTo($value)

			->if($this->testedInstance[2] = $mixed = uniqid())
			->then
				->string($this->testedInstance->invoke(array(), 2))->isEqualTo($mixed)

			->if($this->testedInstance[] = $otherMixed = uniqid())
			->then
				->string($this->testedInstance->invoke(array(), 3))->isEqualTo($otherMixed)

			->if(
				$this->testedInstance[5] = uniqid(),
				$this->testedInstance[] = $lastMixed = uniqid()
			)
			->then
				->boolean(isset($this->testedInstance[4]))->isFalse
				->boolean(isset($this->testedInstance[5]))->isTrue
				->boolean(isset($this->testedInstance[6]))->isTrue
		;
	}

	public function testOffsetGet()
	{
		$this
			->given($invoker = $this->newTestedInstance(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->offsetGet(- rand(1, PHP_INT_MAX));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
				->variable($this->testedInstance->getClosure(rand(0, PHP_INT_MAX)))->isNull()

			->if($this->testedInstance->setClosure($value = function() {}, 0))
			->then
				->object($this->testedInstance[0])->isTestedInstance
				->variable($this->testedInstance->getCurrentCall())->isEqualTo(0)

				->object($this->testedInstance[$call = rand(1, PHP_INT_MAX)])->isTestedInstance
				->variable($this->testedInstance->getCurrentCall())->isEqualTo($call)
		;
	}

	public function testOffsetExists()
	{
		$this
			->given($invoker = $this->newTestedInstance(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->offsetExists(- rand(1, PHP_INT_MAX), function() {});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Call number must be greater than or equal to zero')
				->boolean($this->testedInstance->offsetExists(rand(0, PHP_INT_MAX)))->isFalse

			->if($this->testedInstance->setClosure(function() {}, 0))
			->then
				->boolean(isset($this->testedInstance[0]))->isTrue
				->boolean(isset($this->testedInstance[rand(1, PHP_INT_MAX)]))->isTrue

			->given($this->newTestedInstance(uniqid()))
			->then
				->if($this->testedInstance->setClosure(function() {}, 2))
				->then
					->boolean(isset($this->testedInstance[0]))->isFalse
					->boolean(isset($this->testedInstance[1]))->isFalse
					->boolean(isset($this->testedInstance[2]))->isTrue
					->boolean(isset($this->testedInstance[3]))->isFalse

				->if($this->testedInstance->setClosure(function() {}, 0))
				->then
					->boolean(isset($this->testedInstance[0]))->isTrue
					->boolean(isset($this->testedInstance[1]))->isTrue
					->boolean(isset($this->testedInstance[2]))->isTrue
					->boolean(isset($this->testedInstance[3]))->isTrue
		;
	}

	public function testOffsetUnset()
	{
		$this
			->given($invoker = $this->newTestedInstance(uniqid()))
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

			->if($this->testedInstance->setClosure(function() {}))
			->when(function() use ($invoker) { unset($invoker[0]); })
			->then
				->boolean($this->testedInstance->closureIsSetForCall(0))->isFalse
		;
	}

	public function testInvoke()
	{
		$this
			->given($invoker = $this->newTestedInstance(uniqid()))
			->then
				->exception(function() use ($invoker) {
						$invoker->invoke();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('There is no closure defined for call 0')

			->if(
				$this->testedInstance->setClosure(function($string) { return md5($string); }),
				$this->testedInstance->setClosure(function() use (& $md5) { return $md5 = uniqid(); }, 1),
				$this->testedInstance->setClosure(function() use (& $md5) { return $md5 = uniqid(); }, $call = rand(2, PHP_INT_MAX))
			)
			->then
				->string($this->testedInstance->invoke(array($string = uniqid())))->isEqualTo(md5($string))
				->string($this->testedInstance->invoke(array($string = uniqid()), 0))->isEqualTo(md5($string))
				->string($this->testedInstance->invoke(array($string = uniqid()), 1))->isEqualTo($md5)
				->string($this->testedInstance->invoke(array($string = uniqid())))->isEqualTo(md5($string))
				->string($this->testedInstance->invoke(array($string = uniqid()), 0))->isEqualTo(md5($string))
				->string($this->testedInstance->invoke(array($string = uniqid()), $call))->isEqualTo($md5)
		;
	}

	public function testAtCall()
	{
		$this
			->given($this->newTestedInstance(uniqid()))

			->if($this->testedInstance->setClosure(function () use (& $defaultReturn) { return $defaultReturn = uniqid(); }, 0))
			->then
				->variable($this->testedInstance->getCurrentCall())->isNull()
				->object($this->testedInstance->atCall($call = rand(1, PHP_INT_MAX)))->isTestedInstance
				->integer($this->testedInstance->getCurrentCall())->isEqualTo($call)
		;
	}
}
