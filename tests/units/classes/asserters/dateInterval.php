<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
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
			->if($asserter = new asserters\dateInterval($generator = new asserter\generator()))
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
			->if($asserter = new asserters\dateInterval($generator = new asserter\generator()))
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

	public function testIsLongerThan()
	{
		$this
			->if($asserter = new asserters\dateInterval($generator = new asserter\generator()))
				->exception(function() use ($asserter) { $asserter->isLongerThan(new \DateInterval('P1D')); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateInterval is undefined')
			->if($asserter->setWith(new \DateInterval('P1Y')))
			->then	
				->object($asserter->isLongerThan(new \DateInterval('P1M')))->isIdenticalTo($asserter)
				->exception(function()use($asserter){$asserter->isLongerThan(new \DateInterval('P2Y'));})
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('This interval is not longer than 2 years 0 months 0 days 0 hours 0 minutes 0 seconds')
				->exception(function()use($asserter){$asserter->isLongerThan(new \DateInterval('P1Y'));})
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('This interval is not longer than 1 years 0 months 0 days 0 hours 0 minutes 0 seconds')

			
		;
	}
	public function testIsShorterThan()
	{
		$this
			->if($asserter = new asserters\dateInterval($generator = new asserter\generator()))
				->exception(function() use ($asserter) { $asserter->isShorterThan(new \DateInterval('P1D')); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateInterval is undefined')
			->if($asserter->setWith(new \dateInterval('P2D')))
			->then	
				->object($asserter->isShorterThan(new \dateInterval('P1M')))->isIdenticalTo($asserter)
				->exception(function()use($asserter){$asserter->isShorterThan(new \dateInterval('P1D'));})
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('This interval is not shorter than 0 years 0 months 1 days 0 hours 0 minutes 0 seconds')
				->exception(function()use($asserter){$asserter->isShorterThan(new \dateInterval('P2D'));})
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('This interval is not shorter than 0 years 0 months 2 days 0 hours 0 minutes 0 seconds')

			
		;
	}
	public function testIsAsLongAs()
	{
		$tomorrow = new \dateTime('tomorrow');
		
		$this	
			->if($asserter = new asserters\dateInterval($generator = new asserter\generator()))
				->exception(function() use ($asserter) { $asserter->isShorterThan(new \dateInterval('P1D')); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateInterval is undefined')
			->if($asserter->setWith(new \DateInterval('P1D')))
				->then
				->object($asserter->isAsLongAs(new \DateInterval('P1D')))->isIdenticalTo($asserter)
				->object($asserter->isAsLongAs($tomorrow->diff(new \dateTime('today'))))->isIdenticalTo($asserter)
				->exception(function()use($asserter){$asserter->isAsLongAs(new \dateInterval('PT1S'));})
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('This interval is not equal to 0 years 0 months 0 days 0 hours 0 minutes 1 seconds')
				->exception(function()use($asserter){$asserter->isAsLongAs(new \dateInterval('P2D'));})
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('This interval is not equal to 0 years 0 months 2 days 0 hours 0 minutes 0 seconds')	
		;
	}
	
	public function testIsInRange()
	{
		$this	
			->if($asserter = new asserters\dateInterval($generator = new asserter\generator()))
				->exception(function() use ($asserter) { $asserter->isInRange(new \dateInterval('P1D'),new \dateInterval('P1DT1H')); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Instance of \\dateInterval is undefined')
			->if($asserter->setWith(new \dateInterval('P2D')))
				->object($asserter->isInRange(new \dateInterval('P1D'),new \dateInterval('P1M')))->isIdenticalTo($asserter)
				->exception(function()use($asserter){$asserter->isInRange(new \dateInterval('P3D'),new \dateInterval('P1M'));})
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('This interval is not between 0 years 0 months 3 days 0 hours 0 minutes 0 seconds and 0 years 1 months 0 days 0 hours 0 minutes 0 seconds')
		;
	
	}
}
