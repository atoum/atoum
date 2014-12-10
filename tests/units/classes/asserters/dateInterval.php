<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class dateInterval extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\object');
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

			->given($this->newTestedInstance($generator = new atoum\asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
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
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->object($asserter->setWith($value = new \DateInterval('P0D')))->isIdenticalTo($asserter)
				->object($asserter->getValue())->isIdenticalTo($value)

				->object($asserter->setWith($value = uniqid(), false))->isIdenticalTo($asserter)

			->if(
				$this->calling($locale)->_ = $notDateInterval = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notDateInterval)
				->mock($locale)->call('_')->withArguments('%s is not an instance of \\dateInterval', $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
				->string($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testIsGreaterThan()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isGreaterThan(new \DateInterval('P1D')); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Interval is undefined')

			->if($asserter->setWith(new \DateInterval('P1Y')))
			->then
				->object($asserter->isGreaterThan(new \DateInterval('P1M')))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, & $interval) { $asserter->isGreaterThan($interval = new \DateInterval('P2Y')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not greater than ' . $interval->format('%Y/%M/%D %H:%I:%S'))
		;
	}

	public function testIsGreaterThanOrEqualTo()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isGreaterThanOrEqualTo(new \DateInterval('P1D')); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Interval is undefined')

			->if($asserter->setWith(new \DateInterval('P1Y')))
			->then
				->object($asserter->isGreaterThanOrEqualTo(new \DateInterval('P1M')))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, & $interval) { $asserter->isGreaterThanOrEqualTo($interval = new \DateInterval('P2Y')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not greater than or equal to ' . $interval->format('%Y/%M/%D %H:%I:%S'))
		;
	}

	public function testIsZero()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isZero(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Interval is undefined')
				->exception(function() use ($asserter) { $asserter->isZero; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Interval is undefined')

			->if($asserter->setWith(new \DateInterval('P0Y')))
			->then
				->object($asserter->isZero())->isIdenticalTo($asserter)
				->object($asserter->isZero)->isIdenticalTo($asserter)

			->if($asserter->setWith($interval = new \DateInterval('P1Y')))
			->then
				->exception(function() use ($asserter) { $asserter->isZero(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not equal to zero')
				->exception(function() use ($asserter) { $asserter->isZero; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not equal to zero')
		;
	}

	public function testIsLessThan()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
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
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isLessThanOrEqualTo(new \DateInterval('P1D')); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Interval is undefined')

			->if($asserter->setWith(new \dateInterval('P2D')))
			->then
				->object($asserter->isLessThanOrEqualTo(new \dateInterval('P1M')))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, & $interval) { $asserter->isLessThanOrEqualTo($interval = new \dateInterval('P1D')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not less than or equal to ' . $interval->format('%Y/%M/%D %H:%I:%S'))
		;
	}

	public function testIsEqualTo()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isEqualTo(new \dateInterval('P1D')); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Interval is undefined')

			->if($asserter->setWith(new \DateInterval('P1D')))
			->then
				->object($asserter->isEqualTo(new \DateInterval('P1D')))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, & $interval) { $asserter->isEqualTo($interval = new \dateInterval('PT1S')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('Interval ' . $asserter . ' is not equal to ' . $interval->format('%Y/%M/%D %H:%I:%S'))
		;
	}
}
