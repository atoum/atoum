<?php

namespace mageekguy\atoum\tests\units\asserters\call;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\asserters,
	mageekguy\atoum\asserters\call\arguments as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class invokable
{
	public function __invoke() {}

	public function foo() {}
}

class arguments extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->isSubclassOf('mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$this
			->if($callAsserter = new \mock\mageekguy\atoum\asserters\call())
			->and($asserter = new testedClass($callAsserter))
			->then
				->object($asserter->getCallAsserter())->isIdenticalTo($callAsserter)
		;
	}

	public function testSetWith()
	{
		$this
			->if($callAsserter = new \mock\mageekguy\atoum\asserters\call())
			->and($asserter = new testedClass($callAsserter))
			->then
				->variable($asserter->getCallee())->isNull()
				->exception(function() use ($asserter, & $value) {
							$asserter->setWith($value = uniqid());
						}
					)
						->isInstanceof('mageekguy\atoum\asserter\exception')
						->hasMessage($asserter->getTypeOf($value) . ' is not a test adapter')
				->object($asserter->setWith($adapter = new atoum\test\adapter()))->isIdenticalTo($asserter)
				->object($asserter->getCallee())->isIdenticalTo($adapter)
		;
	}

	public function testArrayAccess()
	{
		$this
			->if($callAsserter = new \mock\mageekguy\atoum\asserters\call())
			->and($asserter = new testedClass($callAsserter))
			->then
				->boolean(isset($asserter[0]))->isFalse()
				->object($asserter[0])->isIdenticalTo($asserter)
				->boolean(isset($asserter[0]))->isFalse()
				->variable($asserter[0] = $value = uniqid())->isEqualTo($value)
				->boolean(isset($asserter[0]))->isTrue()
			->when(function() use ($asserter) { unset($asserter[0]); })
			->then
				->boolean(isset($asserter[0]))->isFalse()
		;
	}

	public function testIsIdenticalTo()
	{
		$this
			->if($callAsserter = new \mock\mageekguy\atoum\asserters\call())
			->and($asserter = new testedClass($callAsserter))
			->then
				->object($asserter[0]->isIdenticalTo($value = rand(0, PHP_INT_MAX)))->isIdenticalTo($asserter)
				->object($argumentAsserter = $asserter->getArgumentAsserter(0))->isCallable()
				->boolean($argumentAsserter(uniqid()))->isFalse()
				->boolean($argumentAsserter((string) $value))->isFalse()
				->boolean($argumentAsserter($value))->isTrue()
		;
	}

	public function testIsEqualTo()
	{
		$this
			->if($callAsserter = new \mock\mageekguy\atoum\asserters\call())
			->and($asserter = new testedClass($callAsserter))
			->then
				->object($asserter[0]->isEqualTo($value = rand(0, PHP_INT_MAX)))->isIdenticalTo($asserter)
				->object($argumentAsserter = $asserter->getArgumentAsserter(0))->isCallable()
				->boolean($argumentAsserter(uniqid()))->isFalse()
				->boolean($argumentAsserter((string) $value))->isTrue()
				->boolean($argumentAsserter($value))->isTrue()
		;
	}

	public function testIsNull()
	{
		$this
			->if($callAsserter = new \mock\mageekguy\atoum\asserters\call())
			->and($asserter = new testedClass($callAsserter))
			->then
				->object($asserter[0]->isNull())->isIdenticalTo($asserter)
				->object($argumentAsserter = $asserter->getArgumentAsserter(0))->isCallable()
				->boolean($argumentAsserter(''))->isFalse()
				->boolean($argumentAsserter(0))->isFalse()
				->boolean($argumentAsserter(false))->isFalse()
				->boolean($argumentAsserter(array()))->isFalse()
				->boolean($argumentAsserter(null))->isTrue()
		;
	}

	public function testIsInstanceOf()
	{
		$this
			->if($callAsserter = new \mock\mageekguy\atoum\asserters\call())
			->and($asserter = new testedClass($callAsserter))
			->then
				->exception(function() use ($asserter) {
							$asserter[0]->isInstanceOf(uniqid());
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Argument of ' . get_class($asserter) . '::isInstanceOf() must be a class instance or a class name')
				->object($asserter[1]->isInstanceOf('stdClass'))->isIdenticalTo($asserter)
				->object($argumentAsserter = $asserter->getArgumentAsserter(1))->isCallable()
				->boolean($argumentAsserter(uniqid()))->isFalse()
				->boolean($argumentAsserter(new \stdClass))->isTrue()
				->boolean($argumentAsserter(new \mock\stdClass()))->isTrue()
		;
	}

	public function testIsCallable()
	{
		$this
			->if($callAsserter = new \mock\mageekguy\atoum\asserters\call())
			->and($asserter = new testedClass($callAsserter))
			->and($invokable = new \mageekguy\atoum\tests\units\asserters\call\invokable())
			->then
				->object($asserter[0]->isCallable())->isIdenticalTo($asserter)
				->object($argumentAsserter = $asserter->getArgumentAsserter(0))->isCallable()
				->boolean($argumentAsserter(uniqid()))->isFalse()
				->boolean($argumentAsserter('md5'))->isTrue()
				->boolean($argumentAsserter(new \stdClass()))->isFalse()
				->boolean($argumentAsserter($invokable))->isTrue()
				->boolean($argumentAsserter(array($invokable, 'foo')))->isTrue()
				->boolean($argumentAsserter(function() {}))->isTrue()
		;
	}

	public function testOnce()
	{
		$this
			->if($callAsserter = new asserters\call\mock($mockAsserter = new asserters\mock()))
			->and($asserter = new testedClass($callAsserter))
			->and($mockAsserter->setWith($mock = new \mock\dummy()))
			->and($asserter->setWith($mock->getMockController()))
			->and($callAsserter->setWith($call = new php\call('foo', null, $mock)))
			->and($mock->foo($random = uniqid()))
			->then
				->object($asserter[0]->isIdenticalTo($argument = uniqid()))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) {
						$asserter->once();
					}
				)
					->isInstanceof('mageekguy\atoum\asserter\exception')
					->hasMessage('function ' . $call . ' is called 0 time instead of 1' . PHP_EOL . '[1] ' . new php\call('foo', array($random)))
			->if($mock->foo($argument))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($mock->foo($argument))
			->then
				->exception(function() use ($asserter) {
						$asserter->once();
					}
				)
					->isInstanceof('mageekguy\atoum\asserter\exception')
					->hasMessage('function ' . $call . ' is called 2 times instead of 1' . PHP_EOL . '[1] ' . new php\call('foo', array($random)) . PHP_EOL . '[2] ' . new php\call('foo', array($argument)) . PHP_EOL . '[3] ' . new php\call('foo', array($argument)))
			->if($mock->getMockController()->resetCalls())
			->and($asserter = new testedClass($callAsserter))
			->and($callAsserter->setWith($call = new php\call('foo', null, $mock)))
			->and($asserter->setWith($mock->getMockController()))
			->and($asserter[0]->isIdenticalTo($argument))
			->and($asserter[1]->isEqualTo($number = rand(0, PHP_INT_MAX)))
			->and($mock->foo($argument, (string) $number))
			->then
				->object($asserter->once())->isIdenticalTo($asserter)
			->if($mock->foo($argument, $number))
			->then
				->exception(function() use ($asserter) {
						$asserter->once();
					}
				)
					->isInstanceof('mageekguy\atoum\asserter\exception')
					->hasMessage('function ' . $call . ' is called 2 times instead of 1' . PHP_EOL . '[1] ' . new php\call('foo', array($argument, (string) $number)) . PHP_EOL . '[2] ' . new php\call('foo', array($argument, $number)))
		;
	}
} 