<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\asserters\phpArray as sut
;

require_once __DIR__ . '/../../runner.php';

class phpArray extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->extends('mageekguy\atoum\asserters\variable')
			->implements('arrayAccess')
		;
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
				->variable($asserter->getKey())->isNull()
				->variable($asserter->getInnerAsserter())->isNull()
				->variable($asserter->getInnerValue())->isNull()
		;
	}

	public function test__get()
	{
		$this
			->if($asserter = new sut($generator = new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->object($asserter->object)->isIdenticalTo($asserter)
				->object($asserter->getInnerAsserter())->isEqualTo($generator->object)
				->object($asserter->object->string)->isIdenticalTo($asserter)
				->object($asserter->getInnerAsserter())->isEqualTo($generator->string)
				->object($asserter->error)->isInstanceOf($generator->error)
				->variable($asserter->getInnerAsserter())->isNull()
			->if($asserter->setWith(array(
						0 => array(
							0 => array(
								1 => array('foo', 'bar')
							),
							1 => array(1, new \mock\object())
						),
						1 => 'foobar'
					)
				)
			)
			->then
				->object($asserter->phpArray[0][0][1]->isEqualTo(array('foo', 'bar')))->isIdenticalTo($asserter)
				->object($asserter->string[1]->isEqualTo('foobar'))->isIdenticalTo($asserter)
			->if($asserter = new sut($generator = new \mock\mageekguy\atoum\asserter\generator()))
			->and($asserter->setWith(array($array1 = array('foo', 'bar'), $array2 = array(1, new \mock\object()))))
			->then
				->object($asserter->phpArray[0]->string[0]->isEqualTo('foo'))->isInstanceOf('mageekguy\atoum\asserters\phpArray')
				->object($asserter->phpArray[1]->isEqualTo($array2))->isInstanceOf('mageekguy\atoum\asserters\phpArray')
		;
	}

	public function testReset()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
				->boolean($asserter->isSetByReference())->isFalse()
				->variable($asserter->getKey())->isNull()
				->variable($asserter->getInnerAsserter())->isNull()
				->variable($asserter->getInnerValue())->isNull()
			->if($asserter->setWith(array()))
			->then
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
				->boolean($asserter->isSetByReference())->isFalse()
				->variable($asserter->getKey())->isNull()
				->variable($asserter->getInnerAsserter())->isNull()
				->variable($asserter->getInnerValue())->isNull()
			->if($reference = range(1, 5))
			->and($asserter->setByReferenceWith($reference))
			->then
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
				->boolean($asserter->isSetByReference())->isFalse()
				->variable($asserter->getKey())->isNull()
				->variable($asserter->getInnerAsserter())->isNull()
				->variable($asserter->getInnerValue())->isNull()
			->if($asserter->object)
			->then
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
				->boolean($asserter->isSetByReference())->isFalse()
				->variable($asserter->getKey())->isNull()
				->variable($asserter->getInnerAsserter())->isNull()
				->variable($asserter->getInnerValue())->isNull()
			->if($asserter->setWith(range(1, 5)))
			->and($asserter->atKey(2))
			->then
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
				->boolean($asserter->isSetByReference())->isFalse()
				->variable($asserter->getKey())->isNull()
				->variable($asserter->getInnerAsserter())->isNull()
				->variable($asserter->getInnerValue())->isNull()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an array'), $asserter->getTypeOf($value)))
				->object($asserter->setWith($value = array()))->isIdenticalTo($asserter)
				->array($asserter->getValue())->isEqualTo($value)
				->variable($asserter->getKey())->isNull()
				->variable($asserter->getInnerAsserter())->isNull()
				->variable($asserter->getInnerValue())->isNull()
				->boolean($asserter->isSetByReference())->isFalse()
			->if($asserter->object)
			->then
				->variable($innerAsserter = $asserter->getInnerAsserter())->isNotNull()
				->object($objectAsserter = $asserter->setWith($object = new \mock\object()))->isIdenticalTo($innerAsserter)
				->object($objectAsserter->getValue())->isIdenticalTo($object)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
				->boolean($asserter->isSetByReference())->isFalse()
				->variable($asserter->getKey())->isNull()
				->variable($asserter->getInnerAsserter())->isNull()
				->variable($asserter->getInnerValue())->isNull()
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($asserter = new sut($generator = new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter[2]; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array(1, 2, $object = new \mock\object(), clone $object)))
			->then
				->exception(function() use ($asserter) { $asserter[2]; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Value %s at key %s is not an array'), $asserter->getTypeOf($object), 2))
				->object($asserter->integer[0])->isIdenticalTo($asserter)
			->if($asserter = new sut($generator = new \mock\mageekguy\atoum\asserter\generator()))
			->and($asserter->setWith(array(1, 2, $object = new \mock\object(), clone $object)))
			->then
				->object($asserter->object[2]->isIdenticalTo($object))->isIdenticalTo($asserter)
				->object($asserter->object[2]->isIdenticalTo($object))->isIdenticalTo($asserter)
				->object($asserter->object[3]->isCloneOf($object))->isIdenticalTo($asserter)
				->object($asserter->object[2])->isIdenticalTo($asserter)
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->and($asserter->setWith($array = array($integer = rand(1, PHP_INT_MAX), 2, $innerArray = array(3, 4, 5, $object))))
			->then
				->object($asserter[2])
					->isIdenticalTo($asserter)
					->array($asserter->getValue())->isEqualTo($innerArray)
			->if($asserter->setWith($array))
			->then
				->object($asserter->integer[0]->isEqualTo($integer))->isIdenticalTo($asserter)
				->object($asserter->object[2][3]->isIdenticalTo($object))->isIdenticalTo($asserter)
				->object($asserter->object[2][3]->isIdenticalTo($object)->integer[0]->isEqualTo($integer))->isIdenticalTo($asserter)
				->object($asserter->object[2][3]->isIdenticalTo($object)->integer($integer)->isEqualTo($integer))
					->isNotIdenticalTo($asserter)
					->isInstanceOf('mageekguy\atoum\asserters\integer')
				->exception(function() use ($asserter) { $asserter->object[2][4]; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has no key %s'), $asserter->getTypeOf($asserter->getInnerValue()), $asserter->getTypeOf(4)))
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter[rand(0, PHP_INT_MAX)] = rand(0, PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Tested array is read only')
		;
	}

	public function testOffsetUnset()
	{
		$this
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) { unset($asserter[rand(0, PHP_INT_MAX)]); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is read only')
		;
	}

	public function testOffsetExists()
	{
		$this
			->if($asserter = new sut(new \mock\mageekguy\atoum\asserter\generator()))
			->then
				->boolean(isset($asserter[rand(0, PHP_INT_MAX)]))->isFalse()
			->if($asserter->setWith(array()))
			->then
				->boolean(isset($asserter[rand(0, PHP_INT_MAX)]))->isFalse()
			->if($asserter->setWith(array(uniqid())))
			->then
				->boolean(isset($asserter[0]))->isTrue()
				->boolean(isset($asserter[rand(1, PHP_INT_MAX)]))->isFalse()
			->if($asserter->setWith(array($key = uniqid() => uniqid())))
			->then
				->boolean(isset($asserter[$key]))->isTrue()
				->boolean(isset($asserter[0]))->isFalse()
				->boolean(isset($asserter[rand(1, PHP_INT_MAX)]))->isFalse()
		;
	}

	public function testHasSize()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasKeys(array(rand(0, PHP_INT_MAX))); })
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

	public function testKeys()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->keys; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->object($array = $asserter->keys)
					->isInstanceOf('mageekguy\atoum\asserters\phpArray')
				->array($array->getValue())
					->isEqualTo(array())
			->if($asserter->setWith(array($key1 = uniqid() => uniqid(), $key2 = uniqid() => uniqid())))
			->then
				->object($array = $asserter->keys)
					->isInstanceOf('mageekguy\atoum\asserters\phpArray')
				->array($array->getValue())
					->isEqualTo(array($key1, $key2))
		;
	}

	public function testSize()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
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

	public function testIsEqualTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isEqualTo(array()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->object($asserter->isEqualTo(array()))->isIdenticalTo($asserter)
			->if($asserter->setWith($array = range(1, 5)))
			->then
				->object($asserter->isEqualTo($array))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->then
				->exception(function() use (& $line, $asserter, & $notEqualValue) { $line = __LINE__; $asserter->isEqualTo($notEqualValue = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf($notEqualValue)) . PHP_EOL . $diff->setExpected($notEqualValue)->setActual($asserter->getValue()))
			->if($asserter->integer)
			->then
				->object($asserter->isEqualTo($array))->isIdenticalTo($asserter)
				->exception(function() use (& $line, $asserter, & $notEqualValue) { $line = __LINE__; $asserter->isEqualTo($notEqualValue = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf($notEqualValue)) . PHP_EOL . $diff->setExpected($notEqualValue)->setActual($asserter->getValue()))
			->if($asserter->integer[2])
			->then
				->object($asserter->isEqualTo(3))->isIdenticalTo($asserter)
				->object($asserter->isNotEqualTo(2))->isIdenticalTo($asserter)
				->object($asserter->isEqualTo(3)->isNotEqualTo(5))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $expectedValue) { $asserter->isEqualTo($expectedValue = rand(4, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter->getTypeOf($asserter->getInnerValue()), $asserter->getTypeOf($expectedValue)) . PHP_EOL . $diff->setExpected($expectedValue)->setActual(3))
		;
	}

	public function testIsNotEqualTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isNotEqualTo(array()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array()))
			->then
				->object($asserter->isNotEqualTo(range(1, 2)))->isIdenticalTo($asserter)
			->if($asserter->setWith($array = range(1, 5)))
			->then
				->object($asserter->isNotEqualTo(array()))->isIdenticalTo($asserter)
			->if($asserter->integer)
			->then
				->object($asserter->isNotEqualTo(array()))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, $array) { $asserter->isNotEqualTo($array); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is equal to %s'), $asserter, $asserter->getTypeOf($array)))
			->if($asserter->integer[2])
			->then
				->object($asserter->isEqualTo(3))->isIdenticalTo($asserter)
				->object($asserter->isEqualTo(3)->isNotEqualTo(5))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $expectedValue) { $asserter->isEqualTo($expectedValue = rand(4, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter->getTypeOf($asserter->getInnerValue()), $asserter->getTypeOf($expectedValue)) . PHP_EOL . $diff->setExpected($expectedValue)->setActual(3))
		;
	}

	public function testIsIdenticalTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isIdenticalTo(new \mock\object()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith(array($object = new \mock\object(), 2)))
			->then
				->object($asserter->isIdenticalTo(array($object, 2)))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $notIdenticalValue, $object) { $asserter->isIdenticalTo($notIdenticalValue = array(clone $object, 2)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not identical to %s'), $asserter, $asserter->getTypeOf($notIdenticalValue)) . PHP_EOL . $diff->setExpected($notIdenticalValue)->setActual($asserter->getValue()))
				->exception(function() use ($asserter, & $notIdenticalValue, $object) { $asserter->isIdenticalTo($notIdenticalValue = array($object, '2')); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not identical to %s'), $asserter, $asserter->getTypeOf($notIdenticalValue)) . PHP_EOL . $diff->setExpected($notIdenticalValue)->setActual($asserter->getValue()))
			->if($asserter->integer)
			->then
				->object($asserter->isIdenticalTo(array($object, 2)))->isIdenticalTo($asserter)
			->if($asserter->integer[1])
			->then
				->object($asserter->isEqualTo(2))->isIdenticalTo($asserter)
				->object($asserter->isEqualTo(2)->isNotEqualTo(5))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $expectedValue) { $asserter->isEqualTo($expectedValue = rand(3, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter->getTypeOf($asserter->getInnerValue()), $asserter->getTypeOf($expectedValue)) . PHP_EOL . $diff->setExpected($expectedValue)->setActual(2))
		;
	}

	public function testIsNotIdenticalTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isNotIdenticalTo(new \mock\object()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Array is undefined')
			->if($asserter->setWith($array = array(1, 2)))
			->then
				->object($asserter->isNotIdenticalTo(array('1', 2)))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, $array) { $asserter->isNotIdenticalTo($array); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is identical to %s'), $asserter, $asserter->getTypeOf($array)))
			->if($asserter->integer)
			->then
				->object($asserter->isNotIdenticalTo(array('1', 2)))->isIdenticalTo($asserter)
			->if($asserter->integer[1])
			->then
				->object($asserter->isEqualTo(2))->isIdenticalTo($asserter)
				->object($asserter->isEqualTo(2)->isNotEqualTo(5))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $expectedValue) { $asserter->isEqualTo($expectedValue = rand(3, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter->getTypeOf($asserter->getInnerValue()), $asserter->getTypeOf($expectedValue)) . PHP_EOL . $diff->setExpected($expectedValue)->setActual(2))
		;
	}
}
