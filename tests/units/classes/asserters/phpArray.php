<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class phpArray extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\variable');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
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
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an array'), $asserter->getTypeOf($value)))
				->object($asserter->setWith($value = array()))->isIdenticalTo($asserter)
				->array($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testHasSize()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->exception(function() use ($asserter, & $size) { $asserter->hasSize($size = rand(1, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has not size %d'), $asserter, $size))
				->object($asserter->hasSize(0))->isIdenticalTo($asserter)
		;
	}

	public function testIsEmpty()
	{
		$this->assert
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(uniqid())))
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not empty'), $asserter))
			->if($asserter->setWith(array()))
			->then
				->object($asserter->isEmpty())->isIdenticalTo($asserter)
		;
	}

	public function testIsNotEmpty()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->isNotEmpty(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
				->if($asserter->setWith(array()))
				->then
					->exception(function() use ($asserter) { $asserter->isNotEmpty(); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s is empty'), $asserter))
				->if($asserter->setWith(array(uniqid())))
				->then
					->object($asserter->isNotEmpty())->isIdenticalTo($asserter)
		;
    }

	public function testContains()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
                ->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(uniqid(), uniqid(), $value = rand(1, PHP_INT_MAX), uniqid(), uniqid())))
			->then
				->exception(function() use ($asserter, & $notInArray) { $asserter->contains($notInArray = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s does not contain %s'), $asserter, $asserter->getTypeOf($notInArray)))
				->object($asserter->contains($value))->isIdenticalTo($asserter)
				->object($asserter->contains((string) $value))->isIdenticalTo($asserter)
        ;
	}

	public function testContainsValues()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
				->if($asserter->setWith(array(1, 2, 3, 4, 5)))
				->then
					->exception(function() use ($asserter) { $asserter->containsValues(array(6)); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s does not contain values %s'), $asserter, $asserter->getTypeOf(array(6))))
					->exception(function() use ($asserter) { $asserter->containsValues(array('6')); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s does not contain values %s'), $asserter, $asserter->getTypeOf(array('6'))))
					->object($asserter->containsValues(array(1)))->isIdenticalTo($asserter)
					->object($asserter->containsValues(array(1, 2, 4)))->isIdenticalTo($asserter)
					->object($asserter->containsValues(array('1', 2, '4')))->isIdenticalTo($asserter)
		;
	}

	public function testNotContainsValues()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(1, 2, 3, 4, 5)))
			->then
				->exception(function() use ($asserter) { $asserter->notContainsValues(array(1, 6)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s should not contain values %s'), $asserter, $asserter->getTypeOf(array(1))))
				->exception(function() use ($asserter) { $asserter->notContainsValues(array('1', '6')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s should not contain values %s'), $asserter, $asserter->getTypeOf(array('1'))))
				->object($asserter->containsValues(array(1)))->isIdenticalTo($asserter)
				->object($asserter->containsValues(array(1, 2, 4)))->isIdenticalTo($asserter)
				->object($asserter->containsValues(array('1', 2, '4')))->isIdenticalTo($asserter)
		;
	}

	public function testStrictlyContainsValues()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
				->if($asserter->setWith(array(1, 2, 3, 4, 5)))
				->then
					->exception(function() use ($asserter) { $asserter->strictlyContainsValues(array(6)); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s does not contain strictly values %s'), $asserter, $asserter->getTypeOf(array(6))))
            ->exception(function() use ($asserter) { $asserter->strictlyContainsValues(array('6')); })
                ->isInstanceOf('mageekguy\atoum\asserter\exception')
                ->hasMessage(sprintf($generator->getLocale()->_('%s does not contain strictly values %s'), $asserter, $asserter->getTypeOf(array('6'))))
            ->exception(function() use ($asserter) { $asserter->strictlyContainsValues(array('1')); })
                ->isInstanceOf('mageekguy\atoum\asserter\exception')
                ->hasMessage(sprintf($generator->getLocale()->_('%s does not contain strictly values %s'), $asserter, $asserter->getTypeOf(array('1'))))
            ->object($asserter->strictlyContainsValues(array(1)))->isIdenticalTo($asserter)
            ->object($asserter->strictlyContainsValues(array(1, 2, 4)))->isIdenticalTo($asserter)
				 ->exception(function() use ($asserter) { $asserter->strictlyContainsValues(array('1', 2, '4')); })
					  ->isInstanceOf('mageekguy\atoum\asserter\exception')
					  ->hasMessage(sprintf($generator->getLocale()->_('%s does not contain strictly values %s'), $asserter, $asserter->getTypeOf(array('1', '4'))))
		;
	}

	public function testStrictlyNotContainsValues()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
				->if($asserter->setWith(array(1, 2, 3, 4, 5)))
				->then
					->exception(function() use ($asserter) { $asserter->strictlyNotContainsValues(array(1)); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s should not contain strictly values %s'), $asserter, $asserter->getTypeOf(array(1))))
					->exception(function() use ($asserter) { $asserter->strictlyNotContainsValues(array(1, '2', 3)); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s should not contain strictly values %s'), $asserter, $asserter->getTypeOf(array(1, 3))))
					->object($asserter->strictlyNotContainsValues(array('1')))->isIdenticalTo($asserter)
					->object($asserter->strictlyNotContainsValues(array(6, 7, '2', 8)))->isIdenticalTo($asserter)
		;
	}

	public function testStrictlyContains ()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->and($asserter->setWith(array(1)))
			->then
				->exception(function() use ($asserter) {$asserter->strictlyContains('1'); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s does not strictly contain %s'), $asserter, $asserter->getTypeOf('1')))
				->object($asserter->strictlyContains(1))->isIdenticalTo($asserter)
		;
	}

	public function testStrictlyNotContains ()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(1)))
			->then
				->exception(function() use ($asserter) {$asserter->strictlyNotContains(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s contains strictly %s'), $asserter, $asserter->getTypeOf(1)))
				->object($asserter->strictlyNotContains('1'))->isIdenticalTo($asserter)
		;
	}

	public function testNotContains()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->notContains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(uniqid(), uniqid(), $inArray = uniqid(), uniqid(), uniqid())))
			->then
				->object($asserter->notContains(uniqid()))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, $inArray) { $asserter->notContains($inArray); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s contains %s'), $asserter, $asserter->getTypeOf($inArray)))
				->exception(function() use($asserter, $inArray){ $asserter->notContains((string) $inArray); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s contains %s'), $asserter, $asserter->getTypeOf((string) $inArray)))
        ;
	}

	public function testHasKey ()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->exception(function() use ($asserter, & $key) { $asserter->hasKey($key = rand(1, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has no key %s'), $asserter, $asserter->getTypeOf($key)))
			->if($asserter->setWith(array(uniqid(), uniqid(), uniqid(), uniqid(), uniqid())))
			->then
				->object($asserter->hasKey(0))->isIdenticalTo($asserter)
				->object($asserter->hasKey(1))->isIdenticalTo($asserter)
				->object($asserter->hasKey(2))->isIdenticalTo($asserter)
				->object($asserter->hasKey(3))->isIdenticalTo($asserter)
				->object($asserter->hasKey(4))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->hasKey(5); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has no key %s'), $asserter, $asserter->getTypeOf(5)))
		;
	}

	public function testNotHasKey ()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->object($asserter->notHasKey(1))->isIdenticalTo($asserter)
			->if($asserter->setWith(array(uniqid(), uniqid(), uniqid(), uniqid(), uniqid())))
			->then
				->exception(function() use ($asserter) { $asserter->notHasKey(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has a key %s'), $asserter, $asserter->getTypeOf(0)))
				->object($asserter->notHasKey(5))->isIdenticalTo($asserter)
		;
	}

	public function testNotHasKeys ()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->object($asserter->notHasKeys(array(1)))->isIdenticalTo($asserter)
				->object($asserter->notHasKeys(array(0, 1)))->isIdenticalTo($asserter)
			->if($asserter->setWith(array(uniqid(), uniqid(), uniqid(), uniqid(), uniqid())))
			->then
				->exception(function() use ($asserter) { $asserter->notHasKeys(array(0, 'premier', 2)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s should not have keys %s'), $asserter, $asserter->getTypeOf(array(0, 2))))
				->object($asserter->notHasKeys(array(5, '6')))->isIdenticalTo($asserter)
		;
	}

	public function testHasKeys ()
	{
		$this
			->if($asserter = new asserters\phpArray($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->exception(function() use ($asserter) { $asserter->hasKeys(array(0)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s should have keys %s'), $asserter, $asserter->getTypeOf(array(0))))
			->if($asserter->setWith(array(uniqid(), uniqid(), uniqid(), uniqid(), uniqid())))
			->then
				->exception(function() use ($asserter) { $asserter->hasKeys(array(0, 'first', 2, 'second')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s should have keys %s'), $asserter, $asserter->getTypeOf(array('first', 'second'))))
			->object($asserter->hasKeys(array(0, 2, 4)))->isIdenticalTo($asserter)
		;
	}
}
