<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class object extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\variable');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\object($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function test__get()
	{
		$this
			->if($asserter = new asserters\object($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->toString; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
				->exception(function() use ($asserter, & $property) { $asserter->{$property = uniqid()}; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $property . '\' does not exist')
			->if($asserter->setWith($this))
			->then
				->object($asserter->toString)->isInstanceOf('mageekguy\atoum\asserters\castToString')
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\object($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an object'), $asserter->getTypeOf($value)))
				->string($asserter->getValue())->isEqualTo($value)
				->object($asserter->setWith($value = $this))->isIdenticalTo($asserter)
				->object($asserter->getValue())->isIdenticalTo($value)
				->object($asserter->setWith($value = uniqid(), false))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testHasSize()
	{
		$this
			->if($asserter = new asserters\object($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($asserter->setWith($this))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has not size %d'), $asserter, 0))
				->object($asserter->hasSize(sizeof($this)))->isIdenticalTo($asserter);
		;
	}

	public function testIsEmpty()
	{
		$this
			->if($asserter = new asserters\object($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($asserter->setWith($this))
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has size %d'), $asserter, sizeof($this)))
			->if($asserter->setWith(new \arrayIterator()))
			->then
				->object($asserter->isEmpty())->isIdenticalTo($asserter)
		;
	}

	public function testToString()
	{
		$this
			->if($asserter = new asserters\object($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->toString(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($asserter->setWith($this))
			->then
				->object($asserter->toString())->isInstanceOf('mageekguy\atoum\asserters\castToString')
		;
	}
}
