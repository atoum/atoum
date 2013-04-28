<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters\phpArray as testedClass
;

require_once __DIR__ . '/../../runner.php';

class phpArray extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\variable');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
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
			->if($asserter = new testedClass($generator = new asserter\generator()))
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
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->exception(function() use ($asserter, & $size) { $asserter->hasSize($size = rand(1, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has not size %d'), $asserter, $size))
				->exception(function() use ($asserter, & $size, & $message) { $asserter->hasSize($size = rand(1, PHP_INT_MAX), $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
				->object($asserter->hasSize(0))->isIdenticalTo($asserter)
		;
	}

	public function testIsEmpty()
	{
		$this->assert
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(uniqid())))
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not empty'), $asserter))
				->exception(function() use ($asserter, & $message) { $asserter->isEmpty($message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
			->if($asserter->setWith(array()))
			->then
				->object($asserter->isEmpty())->isIdenticalTo($asserter)
		;
	}

	public function testIsNotEmpty()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isNotEmpty(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
				->if($asserter->setWith(array()))
				->then
					->exception(function() use ($asserter) { $asserter->isNotEmpty(); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s is empty'), $asserter))
					->exception(function() use ($asserter, & $message) { $asserter->isNotEmpty($message = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($message)
				->if($asserter->setWith(array(uniqid())))
				->then
					->object($asserter->isNotEmpty())->isIdenticalTo($asserter)
		;
	}

	public function testAtKey()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->atKey(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(uniqid(), uniqid(), $data = rand(1, PHP_INT_MAX), uniqid(), uniqid())))
				->object($asserter->atKey(0))->isIdenticalTo($asserter)
				->object($asserter->atKey('0'))->isIdenticalTo($asserter)
				->object($asserter->atKey(1))->isIdenticalTo($asserter)
				->object($asserter->atKey(2))->isIdenticalTo($asserter)
				->object($asserter->atKey(3))->isIdenticalTo($asserter)
				->object($asserter->atKey(4))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $key) { $asserter->atKey($key = rand(5, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has no key %s'), $asserter, $asserter->getTypeOf($key)))
				->exception(function() use ($asserter, & $key, & $message) { $asserter->atKey($key = rand(5, PHP_INT_MAX), $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
		;
	}

	public function testContains()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(uniqid(), uniqid(), $data = rand(1, PHP_INT_MAX), uniqid(), uniqid())))
			->then
				->exception(function() use ($asserter, & $notInArray) { $asserter->contains($notInArray = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s does not contain %s'), $asserter, $asserter->getTypeOf($notInArray)))
				->object($asserter->contains($data))->isIdenticalTo($asserter)
				->object($asserter->contains((string) $data))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, $data) { $asserter->atKey(0)->contains($data); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s does not contain %s at key %s'), $asserter, $asserter->getTypeOf($data), $asserter->getTypeOf(0)))
				->object($asserter->contains($data))->isIdenticalTo($asserter)
				->object($asserter->atKey(2)->contains($data))->isIdenticalTo($asserter)
        ;
	}

	public function testNotContains()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->notContains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(uniqid(), uniqid(), $inArray = uniqid(), uniqid(), uniqid())))
			->then
				->object($asserter->notContains(uniqid()))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, $inArray) { $asserter->notContains($inArray); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s contains %s'), $asserter, $asserter->getTypeOf($inArray)))
				->exception(function() use ($asserter, $inArray, & $message) { $asserter->notContains($inArray, $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
				->exception(function() use($asserter, $inArray){ $asserter->notContains((string) $inArray); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s contains %s'), $asserter, $asserter->getTypeOf((string) $inArray)))
				->exception(function() use($asserter, $inArray, & $message){ $asserter->notContains((string) $inArray, $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
				->object($asserter->atKey(0)->notContains($inArray))->isIdenticalTo($asserter)
				->object($asserter->atKey(1)->notContains($inArray))->isIdenticalTo($asserter)
				->object($asserter->atKey(3)->notContains($inArray))->isIdenticalTo($asserter)
				->object($asserter->atKey(4)->notContains($inArray))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, $inArray) { $asserter->notContains($inArray); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s contains %s'), $asserter, $asserter->getTypeOf($inArray)))
				->exception(function() use ($asserter, $inArray) { $asserter->atKey(2)->notContains($inArray); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s contains %s at key %s'), $asserter, $asserter->getTypeOf($inArray), $asserter->getTypeOf(2)))
				->exception(function() use ($asserter, $inArray) { $asserter->atKey('2')->notContains($inArray); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s contains %s at key %s'), $asserter, $asserter->getTypeOf($inArray), $asserter->getTypeOf('2')))
        ;
	}

	public function testStrictlyContains()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->and($asserter->setWith(array(1, 2, 3, 4, 5, '3')))
			->then
				->exception(function() use ($asserter) {$asserter->strictlyContains('1'); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s does not strictly contain %s'), $asserter, $asserter->getTypeOf('1')))
				->exception(function() use ($asserter, & $message) {$asserter->strictlyContains('1', $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
				->object($asserter->strictlyContains(1))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->atKey(0)->strictlyContains(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s does not strictly contain %s at key %s'), $asserter, $asserter->getTypeOf(2), $asserter->getTypeOf(0)))
				->object($asserter->strictlyContains(2))->isIdenticalTo($asserter)
				->object($asserter->atKey(2)->strictlyContains(3))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->atKey(2)->strictlyContains('3'); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s does not strictly contain %s at key %s'), $asserter, $asserter->getTypeOf('3'), $asserter->getTypeOf(2)))
		;
	}

	public function testStrictlyNotContains()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(1, 2, 3, 4, 5, '6')))
			->then
				->exception(function() use ($asserter) {$asserter->strictlyNotContains(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s strictly contains %s'), $asserter, $asserter->getTypeOf(1)))
				->exception(function() use ($asserter, & $message) {$asserter->strictlyNotContains(1, $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
				->object($asserter->strictlyNotContains('1'))->isIdenticalTo($asserter)
				->object($asserter->atKey(1)->strictlyNotContains(1))->isIdenticalTo($asserter)
				->object($asserter->atKey(2)->strictlyNotContains(1))->isIdenticalTo($asserter)
				->object($asserter->atKey(3)->strictlyNotContains(1))->isIdenticalTo($asserter)
				->object($asserter->atKey(4)->strictlyNotContains(1))->isIdenticalTo($asserter)
				->exception(function() use ($asserter) { $asserter->strictlyNotContains(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s strictly contains %s'), $asserter, $asserter->getTypeOf(1)))
				->exception(function() use ($asserter) { $asserter->atKey(0)->strictlyNotContains(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s strictly contains %s at key %s'), $asserter, $asserter->getTypeOf(1), $asserter->getTypeOf(0)))
				->exception(function() use ($asserter) { $asserter->atKey('0')->strictlyNotContains(1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s strictly contains %s at key %s'), $asserter, $asserter->getTypeOf(1), $asserter->getTypeOf('0')))
		;
	}

	public function testContainsValues()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
				->if($asserter->setWith(array(1, 2, 3, 4, 5)))
				->then
					->exception(function() use ($asserter) { $asserter->containsValues(array(6)); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s does not contain values %s'), $asserter, $asserter->getTypeOf(array(6))))
					->exception(function() use ($asserter, & $message) { $asserter->containsValues(array(6), $message = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($message)
					->exception(function() use ($asserter) { $asserter->containsValues(array('6')); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s does not contain values %s'), $asserter, $asserter->getTypeOf(array('6'))))
					->exception(function() use ($asserter, & $message) { $asserter->containsValues(array('6'), $message = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($message)
					->object($asserter->containsValues(array(1)))->isIdenticalTo($asserter)
					->object($asserter->containsValues(array(1, 2, 4)))->isIdenticalTo($asserter)
					->object($asserter->containsValues(array('1', 2, '4')))->isIdenticalTo($asserter)
		;
	}

	public function testNotContainsValues()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(1, 2, 3, 4, 5)))
			->then
				->exception(function() use ($asserter) { $asserter->notContainsValues(array(1, 6)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s should not contain values %s'), $asserter, $asserter->getTypeOf(array(1))))
				->exception(function() use ($asserter, & $message) { $asserter->notContainsValues(array(1, 6), $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
				->exception(function() use ($asserter, & $message) { $asserter->notContainsValues(array('1', '6'), $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
				->object($asserter->containsValues(array(1)))->isIdenticalTo($asserter)
				->object($asserter->containsValues(array(1, 2, 4)))->isIdenticalTo($asserter)
				->object($asserter->containsValues(array('1', 2, '4')))->isIdenticalTo($asserter)
		;
	}

	public function testStrictlyContainsValues()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
				->if($asserter->setWith(array(1, 2, 3, 4, 5)))
				->then
					->exception(function() use ($asserter) { $asserter->strictlyContainsValues(array(6)); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s does not contain strictly values %s'), $asserter, $asserter->getTypeOf(array(6))))
					->exception(function() use ($asserter, & $message) { $asserter->strictlyContainsValues(array(6), $message = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($message)
					->exception(function() use ($asserter) { $asserter->strictlyContainsValues(array('6')); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s does not contain strictly values %s'), $asserter, $asserter->getTypeOf(array('6'))))
					->exception(function() use ($asserter, & $message) { $asserter->strictlyContainsValues(array('6'), $message = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($message)
					->exception(function() use ($asserter, & $message) { $asserter->strictlyContainsValues(array('1'), $message = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($message)
					->object($asserter->strictlyContainsValues(array(1)))->isIdenticalTo($asserter)
					->object($asserter->strictlyContainsValues(array(1, 2, 4)))->isIdenticalTo($asserter)
					 ->exception(function() use ($asserter, & $message) { $asserter->strictlyContainsValues(array('1', 2, '4'), $message = uniqid()); })
						 ->isInstanceOf('mageekguy\atoum\asserter\exception')
						 ->hasMessage($message)
		;
	}

	public function testStrictlyNotContainsValues()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->contains(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
				->if($asserter->setWith(array(1, 2, 3, 4, 5)))
				->then
					->exception(function() use ($asserter) { $asserter->strictlyNotContainsValues(array(1)); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s should not contain strictly values %s'), $asserter, $asserter->getTypeOf(array(1))))
					->exception(function() use ($asserter, & $message) { $asserter->strictlyNotContainsValues(array(1), $message = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($message)
					->exception(function() use ($asserter, & $message) { $asserter->strictlyNotContainsValues(array(1, '2', 3), $message = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($message)
					->object($asserter->strictlyNotContainsValues(array('1')))->isIdenticalTo($asserter)
					->object($asserter->strictlyNotContainsValues(array(6, 7, '2', 8)))->isIdenticalTo($asserter)
		;
	}

	public function testHasKey()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasKey(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->exception(function() use ($asserter, & $key) { $asserter->hasKey($key = rand(1, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has no key %s'), $asserter, $asserter->getTypeOf($key)))
				->exception(function() use ($asserter, & $key, & $message) { $asserter->hasKey($key = rand(1, PHP_INT_MAX), $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
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
				->exception(function() use ($asserter, & $message) { $asserter->hasKey(5, $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
		;
	}

	public function testNotHasKey()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
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
				->exception(function() use ($asserter, & $message) { $asserter->notHasKey(0, $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
				->object($asserter->notHasKey(5))->isIdenticalTo($asserter)
		;
	}

	public function testNotHasKeys()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
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
				->exception(function() use ($asserter, & $message) { $asserter->notHasKeys(array(0, 'premier', 2), $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
				->object($asserter->notHasKeys(array(5, '6')))->isIdenticalTo($asserter)
		;
	}

	public function testHasKeys()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->exception(function() use ($asserter) { $asserter->hasKeys(array(0)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s should have keys %s'), $asserter, $asserter->getTypeOf(array(0))))
				->exception(function() use ($asserter, & $message) { $asserter->hasKeys(array(0), $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
			->if($asserter->setWith(array(uniqid(), uniqid(), uniqid(), uniqid(), uniqid())))
			->then
				->exception(function() use ($asserter) { $asserter->hasKeys(array(0, 'first', 2, 'second')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s should have keys %s'), $asserter, $asserter->getTypeOf(array('first', 'second'))))
				->exception(function() use ($asserter, & $message) { $asserter->hasKeys(array(0, 'first', 2, 'second'), $message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
				->object($asserter->hasKeys(array(0, 2, 4)))->isIdenticalTo($asserter)
		;
	}

	public function testSize()
	{
		$this
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->size; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->object($integer = $asserter->size)
					->isInstanceOf('mageekguy\atoum\asserters\integer')
				->integer($integer->getValue())
					->isEqualTo(0)
			->if($asserter->setWith(array(uniqid(), uniqid())))
			->then
				->object($integer = $asserter->size)
					->isInstanceOf('mageekguy\atoum\asserters\integer')
				->integer($integer->getValue())
					->isEqualTo(2)
		;
	}
}
