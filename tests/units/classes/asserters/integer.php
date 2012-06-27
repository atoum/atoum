<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\tools\diffs
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
			->if($asserter = new asserters\integer($generator = new asserter\generator()))
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
			->if($asserter = new asserters\integer($generator = new asserter\generator()))
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
			->if($asserter = new asserters\integer($generator = new asserter\generator()))
			->and($asserter->setWith($value = rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->isEqualTo($value))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setReference(- $value)->setData($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isEqualTo(- $value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf(- $value)) . PHP_EOL . $diff)
		;
	}

	public function testIsGreaterThan()
	{
		$this
			->if($asserter = new asserters\integer($generator = new asserter\generator()))
			->and($asserter->setWith($value = rand(1, PHP_INT_MAX - 1)))
			->then
				->object($asserter->isGreaterThan(0))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setReference(PHP_INT_MAX)->setData($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isGreaterThan(PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not greater than %s'), $asserter, $asserter->getTypeOf(PHP_INT_MAX)))
			->if($diff = new diffs\variable())
			->and($diff->setReference($value)->setData($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isGreaterThan($value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not greater than %s'), $asserter, $asserter->getTypeOf($value)))
		;
	}

	public function testIsLowerThan()
	{
		$this
			->if($asserter = new asserters\integer($generator = new asserter\generator()))
			->and($asserter->setWith($value = - rand(1, PHP_INT_MAX - 1)))
			->then
				->object($asserter->isLowerThan(0))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setReference(- PHP_INT_MAX)->setData($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isLowerThan(- PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not lower than %s'), $asserter, $asserter->getTypeOf(- PHP_INT_MAX)))
			->if($diff = new diffs\variable())
			->and($diff->setReference($value)->setData($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isLowerThan($value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not lower than %s'), $asserter, $asserter->getTypeOf($value)))
		;
	}

	public function testIsLessThan()
	{
		$this
			->if($asserter = new asserters\integer($generator = new asserter\generator()))
			->and($asserter->setWith($value = - rand(1, PHP_INT_MAX - 1)))
			->then
				->object($asserter->isLessThan(0))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setReference(- PHP_INT_MAX)->setData($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isLessThan(- PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not lower than %s'), $asserter, $asserter->getTypeOf(- PHP_INT_MAX)))
			->if($diff = new diffs\variable())
			->and($diff->setReference($value)->setData($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isLessThan($value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not lower than %s'), $asserter, $asserter->getTypeOf($value)))
		;
	}

	public function testIsGreaterThanOrEqualTo()
	{
		$this
			->if($asserter = new asserters\integer($generator = new asserter\generator()))
			->and($asserter->setWith($value = rand(1, PHP_INT_MAX - 1)))
			->then
				->object($asserter->isGreaterThanOrEqualTo(0))->isIdenticalTo($asserter)
				->object($asserter->isGreaterThanOrEqualTo($value))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setReference(PHP_INT_MAX)->setData($value))
			->then
				->exception(function() use ($asserter, $value) { $line = __LINE__; $asserter->isGreaterThanOrEqualTo(PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not greater than or equal to %s'), $asserter, $asserter->getTypeOf(PHP_INT_MAX)))
		;
	}

	public function testIsLowerThanOrEqualTo()
	{
		$this
			->if($asserter = new asserters\integer($generator = new asserter\generator()))
			->and($asserter->setWith($value = - rand(1, PHP_INT_MAX - 1)))
			->then
				->object($asserter->isLowerThanOrEqualTo(0))->isIdenticalTo($asserter)
				->object($asserter->isLowerThanOrEqualTo($value))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setReference(- PHP_INT_MAX)->setData($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isLowerThanOrEqualTo(- PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not lower than or equal to %s'), $asserter, $asserter->getTypeOf(- PHP_INT_MAX)))
		;
	}

	public function testisLessThanOrEqualTo()
	{
		$this
			->if($asserter = new asserters\integer($generator = new asserter\generator()))
			->and($asserter->setWith($value = - rand(1, PHP_INT_MAX - 1)))
			->then
				->object($asserter->isLessThanOrEqualTo(0))->isIdenticalTo($asserter)
				->object($asserter->isLessThanOrEqualTo($value))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setReference(- PHP_INT_MAX)->setData($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isLessThanOrEqualTo(- PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not lower than or equal to %s'), $asserter, $asserter->getTypeOf(- PHP_INT_MAX)))
		;
	}
}
