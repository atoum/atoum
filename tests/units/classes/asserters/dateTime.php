<?php

namespace mageekguy\atoum\tests\units\asserters;

use mageekguy\atoum;
use mageekguy\atoum\asserter;
use mageekguy\atoum\tools\variable;

require_once __DIR__ . '/../../runner.php';

class dateTime extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\asserters\phpObject::class);
    }

    public function test__construct()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->getGenerator())->isEqualTo(new atoum\asserter\generator())
                ->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
                ->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
                ->variable($this->testedInstance->getValue())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()

            ->given($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
            ->then
                ->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
                ->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
                ->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
                ->variable($this->testedInstance->getValue())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()
        ;
    }

    public function testSetWith()
    {
        $this
            ->given($asserter = $this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
            ->then
                ->object($asserter->setWith($value = new \DateTime()))->isIdenticalTo($asserter)
                ->object($asserter->getValue())->isIdenticalTo($value)

                ->object($asserter->setWith($value = uniqid(), false))->isIdenticalTo($asserter)
                ->string($asserter->getValue())->isEqualTo($value)

            ->if($this->calling($locale)->_ = $notDatetime = uniqid())
            ->then
                ->exception(function () use ($asserter, & $value) {
                    $asserter->setWith($value = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notDatetime)
                ->mock($locale)->call('_')->withArguments('%s is not an instance of \\dateTime', $asserter)->once
                ->string($asserter->getValue())->isEqualTo($value)
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->setWith($value = new \dateTimeImmutable()))->isTestedInstance
                ->object($this->testedInstance->getValue())->isIdenticalTo($value)
        ;
    }

    public function testHasTimezone()
    {
        $this
            ->given($asserter = $this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasTimezone(new \DateTimezone('Europe/London'));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is not an instance of \\dateTime or \\dateTimeInterface')

            ->if($asserter->setWith($dateTime = new \DateTime('now', $timezone = new \DateTimezone('Europe/Paris'))))
            ->then
                ->object($asserter->hasTimezone($dateTime->getTimezone()))->isIdenticalTo($asserter)

            ->if($this->calling($locale)->_ = $badTimezone = uniqid())
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasTimezone(new \DateTimezone('Europe/London'));
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($badTimezone)
                ->mock($locale)->call('_')->withArguments('Timezone is %s instead of %s', 'Europe/Paris', 'Europe/London')->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasTimezone(new \DateTimezone('Europe/London'), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testHasYear()
    {
        $this
            ->given($asserter = $this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasYear(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is not an instance of \\dateTime or \\dateTimeInterface')

            ->if($asserter->setWith($dateTime = new \DateTime('1976-10-06')))
            ->then
                ->object($asserter->hasYear('1976'))->isIdenticalTo($asserter)
                ->object($asserter->hasYear(1976))->isIdenticalTo($asserter)

            ->if($this->calling($locale)->_ = $badYear = uniqid())
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasYear(1981);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($badYear)
                ->mock($locale)->call('_')->withArguments('Year is %s instead of %s', 1976, 1981)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasYear(1981, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testHasMonth()
    {
        $this
            ->given($asserter = $this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasMonth(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is not an instance of \\dateTime or \\dateTimeInterface')

            ->if($asserter->setWith($dateTime = new \DateTime('1976-09-06')))
            ->then
                ->object($asserter->hasMonth('09'))->isIdenticalTo($asserter)
                ->object($asserter->hasMonth('9'))->isIdenticalTo($asserter)
                ->object($asserter->hasMonth(9))->isIdenticalTo($asserter)

            ->if($this->calling($locale)->_ = $badMonth = uniqid())
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasMonth(1);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($badMonth)
                ->mock($locale)->call('_')->withArguments('Month is %s instead of %02d', '09', 1)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasMonth(1, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testHasDay()
    {
        $this
            ->given($asserter = $this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasDay(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is not an instance of \\dateTime or \\dateTimeInterface')

            ->if($asserter->setWith($dateTime = new \DateTime('1976-10-06')))
            ->then
                ->object($asserter->hasDay('06'))->isIdenticalTo($asserter)
                ->object($asserter->hasDay('6'))->isIdenticalTo($asserter)
                ->object($asserter->hasDay(6))->isIdenticalTo($asserter)

            ->if($this->calling($locale)->_ = $badDay = uniqid())
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasDay(1);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($badDay)
                ->mock($locale)->call('_')->withArguments('Day is %s instead of %02d', '06', 1)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasDay(1, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testHasDate()
    {
        $this
            ->given($asserter = $this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasDate(1976, 10, 6);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is not an instance of \\dateTime or \\dateTimeInterface')

            ->if($asserter->setWith($dateTime = new \DateTime('1976-10-06')))
            ->then
                ->object($asserter->hasDate(1976, 10, 6))->isIdenticalTo($asserter)
                ->object($asserter->hasDate('1976', '10', '6'))->isIdenticalTo($asserter)
                ->object($asserter->hasDate('1976', '10', '06'))->isIdenticalTo($asserter)

            ->if($this->calling($locale)->_ = $badDate = uniqid())
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasDate(1980, 8, 14);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($badDate)
                ->mock($locale)->call('_')->withArguments('Date is %s instead of %s', '1976-10-06', '1980-08-14')->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasDate(1980, 8, 14, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testHasHours()
    {
        $this
            ->given($asserter = $this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasHours(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is not an instance of \\dateTime or \\dateTimeInterface')

            ->if($asserter->setWith($dateTime = new \DateTime('01:02:03')))
            ->then
                ->object($asserter->hasHours('01'))->isIdenticalTo($asserter)
                ->object($asserter->hasHours('1'))->isIdenticalTo($asserter)
                ->object($asserter->hasHours(1))->isIdenticalTo($asserter)

            ->if($this->calling($locale)->_ = $badHours = uniqid())
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasHours(2);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($badHours)
                ->mock($locale)->call('_')->withArguments('Hours are %s instead of %02d', 1, 2)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasHours(2, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testHasMinutes()
    {
        $this
            ->given($asserter = $this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasMinutes(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is not an instance of \\dateTime or \\dateTimeInterface')

            ->if($asserter->setWith($dateTime = new \DateTime('01:02:03')))
            ->then
                ->object($asserter->hasMinutes('02'))->isIdenticalTo($asserter)
                ->object($asserter->hasMinutes('2'))->isIdenticalTo($asserter)
                ->object($asserter->hasMinutes(2))->isIdenticalTo($asserter)

            ->if($this->calling($locale)->_ = $badMinutes = uniqid())
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasMinutes(1);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($badMinutes)
                ->mock($locale)->call('_')->withArguments('Minutes are %s instead of %02d', 2, 1)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasMinutes(1, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testHasSeconds()
    {
        $this
            ->given($asserter = $this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasSeconds(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is not an instance of \\dateTime or \\dateTimeInterface')

            ->if($asserter->setWith($dateTime = new \DateTime('01:02:03')))
            ->then
                ->object($asserter->hasSeconds('03'))->isIdenticalTo($asserter)
                ->object($asserter->hasSeconds('3'))->isIdenticalTo($asserter)
                ->object($asserter->hasSeconds(3))->isIdenticalTo($asserter)

            ->if($this->calling($locale)->_ = $badSeconds = uniqid())
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasSeconds(1);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($badSeconds)
                ->mock($locale)->call('_')->withArguments('Seconds are %s instead of %02d', 3, 1)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasSeconds(1, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testHasTime()
    {
        $this
            ->given($asserter = $this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasTime(1, 2, 3);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is not an instance of \\dateTime or \\dateTimeInterface')

            ->if($asserter->setWith($dateTime = new \DateTime('01:02:03')))
            ->then
                ->object($asserter->hasTime('01', '02', '03'))->isIdenticalTo($asserter)
                ->object($asserter->hasTime('1', '2', '3'))->isIdenticalTo($asserter)
                ->object($asserter->hasTime(1, 2, 3))->isIdenticalTo($asserter)

            ->if($this->calling($locale)->_ = $badTime = uniqid())
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasTime(4, 5, 6);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($badTime)
                ->mock($locale)->call('_')->withArguments('Time is %s instead of %s', '01:02:03', '04:05:06')->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasTime(4, 5, 6, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testHasDateAndTime()
    {
        $this
            ->given($asserter = $this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasDateAndTime(1981, 2, 13, 1, 2, 3);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is not an instance of \\dateTime or \\dateTimeInterface')

            ->if($asserter->setWith($dateTime = new \DateTime('1981-02-13 01:02:03')))
            ->then
                ->object($asserter->hasDateAndTime('1981', '02', '13', '01', '02', '03'))->isIdenticalTo($asserter)
                ->object($asserter->hasDateAndTime('1981', '2', '13', '1', '2', '3'))->isIdenticalTo($asserter)
                ->object($asserter->hasDateAndTime(1981, 2, 13, 1, 2, 3))->isIdenticalTo($asserter)

            ->if($this->calling($locale)->_ = $badDateAndTime = uniqid())
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasDateAndTime(1900, 1, 1, 4, 5, 6);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasmessage($badDateAndTime)
                ->mock($locale)->call('_')->withArguments('Datetime is %s instead of %s', '1981-02-13 01:02:03', '1900-01-01 04:05:06')->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasdateandtime(1900, 1, 1, 4, 5, 6, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasmessage($failMessage)
        ;
    }

    public function testIsImmutable(atoum\locale $locale)
    {
        $this
            ->given(
                $this->newTestedInstance->setLocale($locale),
                $this->calling($locale)->_ = $notImmutable = uniqid()
            )
            ->if($this->testedInstance->setWith($value = new \dateTime()))
            ->then
                ->exception(function () use (& $value) {
                    $this->testedInstance->isImmutable();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notImmutable)
            ->if($this->testedInstance->setWith($value = new \dateTimeImmutable()))
            ->then
                ->object($this->testedInstance->isImmutable())->isTestedInstance
        ;
    }

    public function testIsEqualTo(atoum\locale $locale)
    {
        $this
            ->given(
                $this->newTestedInstance->setLocale($locale),
                $this->calling($locale)->_ = $notEqual = uniqid(),
                $now = date(DATE_ISO8601)
            )
            ->if($this->testedInstance->setWith(new \dateTime($now)))
            ->then
                ->object($this->testedInstance->isEqualTo(new \DateTime($now)))->isTestedInstance
            ->given(
                $nowUTC = '2017-01-17 23:00:00+0000',
                $nowUTCPlusOne = '2017-01-18 00:00:00+0100'
            )
            ->if($this->testedInstance->setWith($value = new \dateTime($nowUTC)))
            ->then
                ->object($this->testedInstance->isEqualTo(new \dateTime($nowUTCPlusOne)))->isTestedInstance
            ->if($this->testedInstance->setWith($value = new \dateTime($nowUTCPlusOne)))
            ->then
                ->object($this->testedInstance->isEqualTo(new \dateTime($nowUTC)))->isTestedInstance
        ;
    }
}
