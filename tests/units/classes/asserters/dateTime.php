<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
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
			->if($asserter = new asserters\dateTime($generator = new asserter\generator()))
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
			->if($asserter = new asserters\dateTime($generator = new asserter\generator()))
			->assert('Set the asserter with something else than a date time trown an exception')
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an instance of \\dateTime'), $asserter->getTypeOf($value)))
				->string($asserter->getValue())->isEqualTo($value)
			->assert('The asserter was returned when it set with a date time')
				->object($asserter->setWith($value = new \dateTime()))->isIdenticalTo($asserter)
				->object($asserter->getValue())->isIdenticalTo($value)
			->assert('It is possible to disable type checking')
				->object($asserter->setWith($value = uniqid(), false))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testHasTimezone()
	{
		$this
			->if($asserter = new asserters\dateTime($generator = new asserter\generator()))
			->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \dateTime('now', $timezone = new \dateTimezone('Europe/Paris'))))
			->then
				->exception(function() use (& $line, & $requiredTimezone, $asserter) { $line = __LINE__; $asserter->hasTimezone($requiredTimezone = new \DateTimezone('Europe/London')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Timezone is %s instead of %s'), $timezone->getName(), $requiredTimezone->getName()))
				->object($asserter->hasTimezone($dateTime->getTimezone()))->isIdenticalTo($asserter);
		;
	}

	public function testIsInYear()
	{
		$this
			->if($asserter = new asserters\dateTime($generator = new asserter\generator()))
			->exception(function() use ($asserter) { $asserter->isInYear(rand(0, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \dateTime('1976-10-06')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isInYear(2011); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Year is %s instead of %s'), 2011, 1976))
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isInYear(76); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Year is %s instead of %s'), 76, 1976))
				->object($asserter->isInYear(1976))->isIdenticalTo($asserter)
		;
	}

	public function testIsInMonth()
	{
		$this
			->if($asserter = new asserters\dateTime($generator = new asserter\generator()))
			->exception(function() use ($asserter) { $asserter->isInMonth(rand(0, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \dateTime('1976-10-06')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isInMonth(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Month is %s instead of %s'), 1, 10))
				->object($asserter->isInMonth(10))->isIdenticalTo($asserter)
			->if($asserter->setWith($dateTime = new \dateTime('1980-08-14')))
			->then
				->object($asserter->isInMonth('08'))->isIdenticalTo($asserter)
				->object($asserter->isInMonth('8'))->isIdenticalTo($asserter)
				->object($asserter->isInMonth(8))->isIdenticalTo($asserter)
		;
	}

	public function testIsInDay()
	{
		$this
			->if($asserter = new asserters\dateTime($generator = new asserter\generator()))
			->exception(function() use ($asserter) { $asserter->isInDay(rand(0, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \dateTime('1976-10-06')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isInDay(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Day is %s instead of %s'), 1, 6))
				->object($asserter->isInDay('06'))->isIdenticalTo($asserter)
				->object($asserter->isInDay('6'))->isIdenticalTo($asserter)
				->object($asserter->isInDay(6))->isIdenticalTo($asserter)
		;
	}

	public function testHasDate()
	{
		$this
			->if($asserter = new asserters\dateTime($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasDate(1976, 10, 6); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Instance of \\dateTime is undefined')
			->if($asserter->setWith($dateTime = new \dateTime('1976-10-06')))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasDate(1980, 8, 14); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Date is %s instead of %s'), '1980-08-14', '1976-10-06'))
				->object($asserter->hasDate(1976, 10, 6))->isIdenticalTo($asserter)
				->object($asserter->hasDate('1976', '10', '6'))->isIdenticalTo($asserter)
				->object($asserter->hasDate('1976', '10', '06'))->isIdenticalTo($asserter)
			->if($asserter->setWith($dateTime = new \dateTime('1980-08-14')))
			->then
				->object($asserter->hasDate(1980, 8, 14))->isIdenticalTo($asserter)
				->object($asserter->hasDate('1980', '8', '14'))->isIdenticalTo($asserter)
				->object($asserter->hasDate('1980', '08', '14'))->isIdenticalTo($asserter)
		;
	}
}
