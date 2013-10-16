<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters\dateInterval as sut
;

require_once __DIR__ . '/../../runner.php';

class dateInterval extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\object');
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
			->assert('Set the asserter with something else than a date interval trown an exception')
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an instance of \\dateInterval'), $asserter->getTypeOf($value)))
				->string($asserter->getValue())->isEqualTo($value)
			->assert('The asserter was returned when it set with a date time')
				->object($asserter->setWith($value = new \DateInterval('P0D')))->isIdenticalTo($asserter)
				->object($asserter->getValue())->isIdenticalTo($value)
			->assert('It is possible to disable type checking')
				->object($asserter->setWith($value = uniqid(), false))->isIdenticalTo($asserter)

		;
	}

	public function testIsGreaterThan()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
				->exception(function() use ($asserter) { $asserter->isGreaterThan(new \DateInterval('P1D')); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Interval is undefined')
			->if($asserter->setWith(new \DateInterval('P1Y')))
			->then
				->object($asserter->isGreaterThan(new \DateInterval('P1M')))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $interval) { $asserter->isGreaterThan($interval = new \DateInterval('P2Y')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not greater than ' . $interval->format('%Y/%M/%D %H:%I:%S'))
				->exception(function() use ($asserter, & $interval) { $asserter->isGreaterThan($interval = new \DateInterval('P1Y')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not greater than ' . $interval->format('%Y/%M/%D %H:%I:%S'))
		;
	}

	public function testIsGreaterThanOrEqualTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
				->exception(function() use ($asserter) { $asserter->isGreaterThanOrEqualTo(new \DateInterval('P1D')); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Interval is undefined')
			->if($asserter->setWith(new \DateInterval('P1Y')))
			->then
				->object($asserter->isGreaterThanOrEqualTo(new \DateInterval('P1M')))->isIdenticalTo($asserter)
				->object($asserter->isGreaterThanOrEqualTo(new \DateInterval('P1Y')))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $interval) { $asserter->isGreaterThanOrEqualTo($interval = new \DateInterval('P2Y')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not greater than or equal to ' . $interval->format('%Y/%M/%D %H:%I:%S'))
		;
	}

	public function testIsZero()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isZero(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Interval is undefined')
			->if($asserter->setWith(new \DateInterval('P0Y')))
			->then
				->object($asserter->isZero())->isIdenticalTo($asserter)
			->if($asserter->setWith($interval = new \DateInterval('P1Y')))
			->then
				->exception(function() use ($asserter) { $asserter->isZero(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not equal to zero')
		;
	}

	public function testIsLessThan()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
				->exception(function() use ($asserter) { $asserter->isLessThan(new \DateInterval('P1D')); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Interval is undefined')
			->if($asserter->setWith(new \dateInterval('P2D')))
			->then
				->object($asserter->isLessThan(new \dateInterval('P1M')))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $interval) { $asserter->isLessThan($interval = new \dateInterval('P1D')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not less than ' . $interval->format('%Y/%M/%D %H:%I:%S'))
				->exception(function() use ($asserter, & $interval) { $asserter->isLessThan($interval = new \dateInterval('P2D')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not less than ' . $interval->format('%Y/%M/%D %H:%I:%S'))
		;
	}

	public function testIsLessThanOrEqualTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
				->exception(function() use ($asserter) { $asserter->isLessThanOrEqualTo(new \DateInterval('P1D')); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Interval is undefined')
			->if($asserter->setWith(new \dateInterval('P2D')))
			->then
				->object($asserter->isLessThanOrEqualTo(new \dateInterval('P1M')))->isIdenticalTo($asserter)
				->object($asserter->isLessThanOrEqualTo(new \dateInterval('P2D')))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $interval) { $asserter->isLessThanOrEqualTo($interval = new \dateInterval('P1D')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not less than or equal to ' . $interval->format('%Y/%M/%D %H:%I:%S'))
		;
	}

	public function testIsEqualTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
				->exception(function() use ($asserter) { $asserter->isEqualTo(new \dateInterval('P1D')); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Interval is undefined')
			->if($asserter->setWith(new \DateInterval('P1D')))
				->then
				->object($asserter->isEqualTo(new \DateInterval('P1D')))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $interval) { $asserter->isEqualTo($interval = new \dateInterval('PT1S')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not equal to ' . $interval->format('%Y/%M/%D %H:%I:%S'))
				->exception(function() use ($asserter, & $interval) { $asserter->isEqualTo($interval = new \dateInterval('P2D')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not equal to ' . $interval->format('%Y/%M/%D %H:%I:%S'))
		;
	}
}
