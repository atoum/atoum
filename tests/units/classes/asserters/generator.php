<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class generator extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\iterator');
	}

	public function test__construct()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
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

	public function testUsage()
	{
		$generator = function() {
			for ($i=0; $i<3; $i++) {
				yield ($i+1);
			}
		};

		$this
			->generator($generator())
				->yields->isEqualTo(1)
				->yields->isEqualTo(2)
				->yields->isEqualTo(3)
				->yields->isNull(4)
			->generator($generator())
				->size->isEqualTo(3)
		;
	}

	public function testYields()
	{
		$generator = function() {
			for ($i=0; $i<10; $i++) {
				yield ($i+1);
			}
		};

		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->object($asserter->setWith($generator()))->isIdenticalTo($asserter)

			->when($yieldAsserter = $asserter->yields)
				->object($yieldAsserter)->isInstanceOf('\mageekguy\atoum\asserters\variable')
				->integer($yieldAsserter->getValue())->isEqualTo(1)

			->when($yieldAsserter = $asserter->yields)
				->object($yieldAsserter)->isInstanceOf('\mageekguy\atoum\asserters\variable')
				->integer($yieldAsserter->getValue())->isEqualTo(2)
		;
	}

	public function testSetWith()
	{
		$generator = function() {
			for ($i=0; $i<10; $i++) {
				yield ($i+1);
			}
		};

		$notAgenerator = function() {
			for ($i=0; $i<10; $i++) {
			}
		};

		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->object($asserter->setWith($generator()))->isIdenticalTo($asserter)

			->then
				->exception(function() use ($asserter) { $asserter->setWith(true); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage("boolean(true) is not an object")

			->then
				->exception(function() use ($asserter, $notAgenerator) { $asserter->setWith($notAgenerator()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage("null is not an object")

			->then
				->exception(function() use ($asserter) { $asserter->setWith(new \stdClass()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage("object(stdClass) is not an iterator")

			->then
				->exception(function() use ($asserter) { $asserter->setWith(new \ArrayIterator()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage("object(ArrayIterator) is not a generator")
		;
	}
}
