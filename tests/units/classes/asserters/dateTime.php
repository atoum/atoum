<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters\dateTime as sut
;

require_once __DIR__ . '/../../runner.php';

class dateTime extends atoum\test
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
			->assert('Set the asserter with something else than a date time trown an exception')
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an instance of \\dateTime'), $asserter->getTypeOf($value)))
				->string($asserter->getValue())->isEqualTo($value)
			->assert('The asserter was returned when it set with a date time')
				->object($asserter->setWith($value = new \DateTime()))->isIdenticalTo($asserter)
				->object($asserter->getValue())->isIdenticalTo($value)
			->assert('It is possible to disable type checking')
				->object($asserter->setWith($value = uniqid(), false))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testHasTimezone()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \DateTime('now', $timezone = new \DateTimezone('Europe/Paris'))))
			->then
				->exception(function() use (& $line, & $requiredTimezone, $asserter) { $line = __LINE__; $asserter->hasTimezone($requiredTimezone = new \DateTimezone('Europe/London')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Timezone is %s instead of %s'), $timezone->getName(), $requiredTimezone->getName()))
				->object($asserter->hasTimezone($dateTime->getTimezone()))->isIdenticalTo($asserter);
		;
	}

	public function testHasYear()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->exception(function() use ($asserter) { $asserter->hasYear(rand(0, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \DateTime('1976-10-06')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasYear(1981); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Year is %s instead of %s'), 1976, 1981))
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasYear(76); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Year is %s instead of %s'), 1976, 76))
				->object($asserter->hasYear('1976'))->isIdenticalTo($asserter)
				->object($asserter->hasYear(1976))->isIdenticalTo($asserter)
		;
	}

	public function testHasMonth()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->exception(function() use ($asserter) { $asserter->hasMonth(rand(0, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \DateTime('1976-09-06')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasMonth(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Month is %02d instead of %02d'), 9, 1))
				->object($asserter->hasMonth(9))->isIdenticalTo($asserter)
			->if($asserter->setWith($dateTime = new \DateTime('1980-08-14')))
			->then
				->object($asserter->hasMonth('08'))->isIdenticalTo($asserter)
				->object($asserter->hasMonth('8'))->isIdenticalTo($asserter)
				->object($asserter->hasMonth(8))->isIdenticalTo($asserter)
		;
	}

	public function testHasDay()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->exception(function() use ($asserter) { $asserter->hasDay(rand(0, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \DateTime('1976-10-06')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasDay(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Day is %02d instead of %02d'), 6, 1))
				->object($asserter->hasDay('06'))->isIdenticalTo($asserter)
				->object($asserter->hasDay('6'))->isIdenticalTo($asserter)
				->object($asserter->hasDay(6))->isIdenticalTo($asserter)
		;
	}

	public function testHasDate()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasDate(1976, 10, 6); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \DateTime('1976-10-06')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasDate(1980, 8, 14); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Date is %s instead of %s'), '1976-10-06', '1980-08-14'))
				->object($asserter->hasDate(1976, 10, 6))->isIdenticalTo($asserter)
				->object($asserter->hasDate('1976', '10', '6'))->isIdenticalTo($asserter)
				->object($asserter->hasDate('1976', '10', '06'))->isIdenticalTo($asserter)
		;
	}

	public function testHasHours()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->exception(function() use ($asserter) { $asserter->hasHours(rand(0, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \DateTime('01:02:03')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasHours(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Hours are %02d instead of %02d'), 1, 2))
				->object($asserter->hasHours('01'))->isIdenticalTo($asserter)
				->object($asserter->hasHours('1'))->isIdenticalTo($asserter)
				->object($asserter->hasHours(1))->isIdenticalTo($asserter)
		;
	}

	public function testHasMinutes()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->exception(function() use ($asserter) { $asserter->hasMinutes(rand(0, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \DateTime('01:02:03')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasMinutes(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Minutes are %02d instead of %02d'), 2, 1))
				->object($asserter->hasMinutes('02'))->isIdenticalTo($asserter)
				->object($asserter->hasMinutes('2'))->isIdenticalTo($asserter)
				->object($asserter->hasMinutes(2))->isIdenticalTo($asserter)
		;
	}

	public function testHasSeconds()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->exception(function() use ($asserter) { $asserter->hasSeconds(rand(0, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \DateTime('01:02:03')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasSeconds(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Seconds are %02d instead of %02d'), 3, 1))
				->object($asserter->hasSeconds('03'))->isIdenticalTo($asserter)
				->object($asserter->hasSeconds('3'))->isIdenticalTo($asserter)
				->object($asserter->hasSeconds(3))->isIdenticalTo($asserter)
		;
	}

	public function testHasTime()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasTime(1, 2, 3); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \DateTime('01:02:03')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasTime(4, 5, 6); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Time is %s instead of %s'), '01:02:03', '04:05:06'))
				->object($asserter->hasTime('01', '02', '03'))->isIdenticalTo($asserter)
				->object($asserter->hasTime('1', '2', '3'))->isIdenticalTo($asserter)
				->object($asserter->hasTime(1, 2, 3))->isIdenticalTo($asserter)
		;
	}

	public function testHasDateAndTime()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasDateAndTime(1981, 2, 13, 1, 2, 3); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \DateTime('1981-02-13 01:02:03')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasDateAndTime(1900, 1, 1, 4, 5, 6); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Datetime is %s instead of %s'), '1981-02-13 01:02:03', '1900-01-01 04:05:06'))
				->object($asserter->hasDateAndTime('1981', '02', '13', '01', '02', '03'))->isIdenticalTo($asserter)
				->object($asserter->hasDateAndTime('1981', '2', '13', '1', '2', '3'))->isIdenticalTo($asserter)
				->object($asserter->hasDateAndTime(1981, 2, 13, 1, 2, 3))->isIdenticalTo($asserter)
		;
	}
}
