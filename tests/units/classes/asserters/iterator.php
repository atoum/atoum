<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters\iterator as sut
;

require_once __DIR__ . '/../../runner.php';

class iterator extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\object');
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()
		;
	}

	public function test__get()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->toString; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
				->exception(function() use ($asserter, & $property) { $asserter->{$property = uniqid()}; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $property . '\' does not exist')
			->if($asserter->setWith($iterator = new \mock\iterator()))
			->then
				->exception(function() use ($asserter) { $asserter->toString; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s could not be converted to string', $asserter->getAnalyzer()->getTypeOf($iterator)))
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an object'), $asserter->getAnalyzer()->getTypeOf($value)))
				->string($asserter->getValue())->isEqualTo($value)
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = $this); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an iterator'), $asserter->getAnalyzer()->getTypeOf($value)))
				->object($asserter->getValue())->isIdenticalTo($value)
				->object($asserter->setWith($value = new \mock\iterator()))->isIdenticalTo($asserter)
				->iterator($asserter->getValue())->isIdenticalTo($value)
				->object($asserter->setWith($value = uniqid(), false))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testHasSize()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($iterator = new \mock\iterator())
			->and($this->calling($iterator)->valid = false)
			->and($this->calling($iterator)->valid[1] = true)
			->and($this->calling($iterator)->valid[2] = true)
			->and($asserter->setWith($iterator))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has size %d, expected size %d'), $asserter, 2, 0))
			->if($iterator = new \mock\iterator())
			->and($this->calling($iterator)->valid = false)
			->and($this->calling($iterator)->valid[1] = true)
			->and($this->calling($iterator)->valid[2] = true)
			->and($asserter->setWith($iterator))
			->then
				->object($asserter->hasSize(2))->isIdenticalTo($asserter);
		;
	}

	public function testIsEmpty()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($iterator = new \mock\iterator())
			->and($this->calling($iterator)->valid = false)
			->and($this->calling($iterator)->valid[1] = true)
			->and($this->calling($iterator)->valid[2] = true)
			->and($asserter->setWith($iterator))
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has size %d'), $asserter, 2))
			->if($iterator = new \mock\iterator())
			->and($this->calling($iterator)->valid = false)
			->and($asserter->setWith($iterator))
			->then
				->object($asserter->isEmpty())->isIdenticalTo($asserter)
		;
	}

	public function testIsCloneOf()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isCloneOf($asserter); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($asserter->setWith($iterator = new \mock\iterator()))
			->then
				->exception(function() use ($asserter, $iterator) { $asserter->isCloneOf($iterator); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not a clone of %s'), $asserter, $asserter->getAnalyzer()->getTypeOf($iterator)))
			->if($clonedIterator = clone $iterator)
			->then
				->object($asserter->isCloneOf($clonedIterator))->isIdenticalTo($asserter)
		;
	}

	public function testToString()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->toString(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($asserter->setWith($iterator = new \mock\iterator()))
			->then
				->exception(function() use ($asserter) { $asserter->toString; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s could not be converted to string', $asserter->getAnalyzer()->getTypeOf($iterator)))
		;
	}

	public function testIsInstanceOf()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) {
						$asserter->isInstanceOf($asserter);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($asserter->setWith($iterator = new \mock\iterator()))
			->then
				->exception(function() use ($asserter) {
						$asserter->isInstanceOf($asserter);
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an instance of %s'), $asserter->getAnalyzer()->getTypeOf($iterator), $asserter->getAnalyzer()->getTypeOf($asserter)))
				->object($asserter->isInstanceOf($iterator))->isIdenticalTo($asserter)
		;
	}

	public function testIsNotInstanceOf()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) {
						$asserter->isNotInstanceOf($asserter);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($asserter->setWith($iterator = new \mock\iterator()))
			->then
				->exception(function() use ($asserter, $iterator) {
						$asserter->isNotInstanceOf($iterator);
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is an instance of %1$s'), $asserter->getAnalyzer()->getTypeOf($iterator)))
				->object($asserter->isNotInstanceOf($asserter))->isIdenticalTo($asserter)
		;
	}
}
