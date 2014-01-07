<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs
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
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
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
			->given($asserter = $this->newTestedInstance(new asserter\generator()))

			->exception(function() use ($asserter, & $property) { $asserter->{$property = uniqid()}; })
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Asserter \'' . $property . '\' does not exist')

			->given($asserter = $this->newTestedInstance(new asserter\generator()))

			->exception(function() use ($asserter) { $asserter->toString; })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Object is undefined')

			->if($asserter->setWith($this))
			->then
				->object($asserter->toString)->isInstanceOf('mageekguy\atoum\asserters\castToString')
				->object($asserter->tostring)->isInstanceOf('mageekguy\atoum\asserters\castToString')
				->object($asserter->TOSTRING)->isInstanceOf('mageekguy\atoum\asserters\castToString')

			->given($asserter = $this->newTestedInstance(new asserter\generator()))

			->exception(function() use ($asserter) { $asserter->isEmpty; })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Object is undefined')

			->if($asserter->setWith(new \arrayIterator()))
			->then
				->object($asserter->isEmpty)->isTestedInstance
				->object($asserter->isempty)->isTestedInstance
				->object($asserter->ISeMPTY)->isTestedInstance

			->given($asserter = $this->newTestedInstance(new asserter\generator()))

			->exception(function() use ($asserter) { $asserter->isTestedInstance; })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Object is undefined')

			->if($asserter->setWithTest($this)->setWith($asserter))
			->then
				->object($asserter->isTestedInstance)->isTestedInstance
				->object($asserter->istestedinstance)->isTestedInstance
				->object($asserter->IStESTEDiNSTANCE)->isTestedInstance

			->given($asserter = $this->newTestedInstance(new asserter\generator()))

			->exception(function() use ($asserter) { $asserter->isNotTestedInstance; })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Object is undefined')

			->if($asserter->setWithTest($this)->setWith($this))
			->then
				->object($asserter->isNotTestedInstance)->isTestedInstance
				->object($asserter->isnottestedinstance)->isTestedInstance
				->object($asserter->ISNottESTEDiNSTANCE)->isTestedInstance

			->given($asserter = $this->newTestedInstance(new asserter\generator()))

			->exception(function() use ($asserter) { $asserter->isInstanceOfTestedClass; })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Object is undefined')

			->if($asserter->setWithTest($this)->setWith($asserter))
			->then
				->object($asserter->isInstanceOfTestedClass)->isTestedInstance
				->object($asserter->isinstanceoftestedclass)->isTestedInstance
				->object($asserter->ISiNSTANCEoFtESTEDcLASS)->isTestedInstance
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
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
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($asserter->setWith($this))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has size %d, expected size %d'), $asserter, sizeof($this), 0))
				->object($asserter->hasSize(sizeof($this)))->isIdenticalTo($asserter);
		;
	}

	public function testIsEmpty()
	{
		$this
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
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

	public function testIsCloneOf()
	{
		$this
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isCloneOf($asserter); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($asserter->setWith($test = $this))
			->then
				->exception(function() use ($asserter, $test) { $asserter->isCloneOf($test); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not a clone of %s'), $asserter, $asserter->getTypeOf($test)))
			->if($clonedTest = clone $test)
			->then
				->object($asserter->isCloneOf($clonedTest))->isIdenticalTo($asserter)
		;
	}

	public function testIsTestedInstance()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))

			->exception(function() use ($asserter) { $asserter->isTestedInstance(); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Object is undefined')

			->if($asserter->setWith($this->testedInstance))
			->then
				->exception(function() use ($asserter) { $asserter->isTestedInstance(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Tested instance is undefined in the test')

			->if($asserter->setWithTest($this))
			->then
				->object($asserter->isTestedInstance())->isIdenticalTo($asserter)

			->if($asserter->setWith($notTestedInstance = new \stdClass()))
			->then
				->exception(function() use ($asserter) { $asserter->isTestedInstance(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
		;
	}

	public function testIsNotTestedInstance()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))

			->exception(function() use ($asserter) { $asserter->isNotTestedInstance(); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Object is undefined')

			->if($asserter->setWith(clone $this->testedInstance))
			->then
				->exception(function() use ($asserter) { $asserter->isNotTestedInstance(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Tested instance is undefined in the test')

			->if($asserter->setWithTest($this))
			->then
				->object($asserter->isNotTestedInstance())->isIdenticalTo($asserter)

			->if($asserter->setWith($this->testedInstance))
			->then
				->exception(function() use ($asserter) { $asserter->isNotTestedInstance(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
		;
	}

	public function testIsInstanceOfTestedClass()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))

			->exception(function() use ($asserter) { $asserter->isInstanceOfTestedClass(); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Object is undefined')

			->if($asserter->setWith(clone $this->testedInstance))
			->then
				->exception(function() use ($asserter) { $asserter->isInstanceOfTestedClass(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Tested instance is undefined in the test')

			->if($asserter->setWithTest($this))
			->then
				->object($asserter->isInstanceOfTestedClass())->isIdenticalTo($asserter)

			->if($asserter->setWith($this))
			->then
				->exception(function() use ($asserter) { $asserter->isInstanceOfTestedClass(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
		;
	}

	public function testToString()
	{
		$this
			->if($asserter = $this->newTestedInstance(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->toString(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
			->if($asserter->setWith($this))
			->then
				->object($asserter->toString())->isInstanceOf('mageekguy\atoum\asserters\castToString')
		;
	}

	public function testIsNotInstanceOf()
	{
		$this
			->object(new \stdClass())
				->isNotInstanceOf('exception')
			->if($asserter = $this->newTestedInstance(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->object(new \stdClass())->isNotInstanceOf('\stdClass'); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('object(stdClass) is an instance of \stdClass')
				->exception(function() use ($asserter) { $asserter->object(new \stdClass())->isNotInstanceOf(new \stdClass()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage('object(stdClass) is an instance of object(stdClass)')
			;
	}
}
