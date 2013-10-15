<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\asserters\integer as sut
;

require_once __DIR__ . '/../../runner.php';

class integer extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\variable');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an integer'), $asserter->getTypeOf($value)))
				->string($asserter->getValue())->isEqualTo($value)
			->object($asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($asserter)
			->integer($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testIsEqualTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->isEqualTo($value))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setExpected(- $value)->setActual($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isEqualTo(- $value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf(- $value)) . PHP_EOL . $diff)
		;
	}

	public function testIsGreaterThan()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = rand(1, PHP_INT_MAX - 1)))
			->then
				->object($asserter->isGreaterThan(0))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setExpected(PHP_INT_MAX)->setActual($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isGreaterThan(PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not greater than %s'), $asserter, $asserter->getTypeOf(PHP_INT_MAX)))
			->if($diff = new diffs\variable())
			->and($diff->setExpected($value)->setActual($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isGreaterThan($value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not greater than %s'), $asserter, $asserter->getTypeOf($value)))
		;
	}

	public function testIsLessThan()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = - rand(1, PHP_INT_MAX - 1)))
			->then
				->object($asserter->isLessThan(0))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setExpected(- PHP_INT_MAX)->setActual($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isLessThan(- PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not less than %s'), $asserter, $asserter->getTypeOf(- PHP_INT_MAX)))
			->if($diff = new diffs\variable())
			->and($diff->setExpected($value)->setActual($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isLessThan($value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not less than %s'), $asserter, $asserter->getTypeOf($value)))
		;
	}

	public function testIsGreaterThanOrEqualTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = rand(1, PHP_INT_MAX - 1)))
			->then
				->object($asserter->isGreaterThanOrEqualTo(0))->isIdenticalTo($asserter)
				->object($asserter->isGreaterThanOrEqualTo($value))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setExpected(PHP_INT_MAX)->setActual($value))
			->then
				->exception(function() use ($asserter, $value) { $line = __LINE__; $asserter->isGreaterThanOrEqualTo(PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not greater than or equal to %s'), $asserter, $asserter->getTypeOf(PHP_INT_MAX)))
		;
	}

	public function testIsLessThanOrEqualTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = - rand(1, PHP_INT_MAX - 1)))
			->then
				->object($asserter->isLessThanOrEqualTo(0))->isIdenticalTo($asserter)
				->object($asserter->isLessThanOrEqualTo($value))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setExpected(- PHP_INT_MAX)->setActual($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isLessThanOrEqualTo(- PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not less than or equal to %s'), $asserter, $asserter->getTypeOf(- PHP_INT_MAX)))
		;
	}
}
