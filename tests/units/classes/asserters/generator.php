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

	/**
	 * @php >= 7.0
	 */
	public function testUsage()
	{
		if (version_compare(PHP_VERSION, '7.0') >= 0) {
			$generator = eval(<<<'PHP'
return function() {
    for ($i=0; $i<3; $i++) {
        yield ($i+1);
    }

    return 42;
};
PHP
			);
		}

		$this
			->generator($generator())
				->yields->isEqualTo(1)
				->yields->isEqualTo(2)
				->yields->isEqualTo(3)
				->yields->isNull()
				->returns->isEqualTo(42)
			->generator($generator())
				->size->isEqualTo(3)
		;
	}

	/**
	 * @php < 7.0
	 */
	public function testReturnsBeforePhp7()
	{
		$generator = function() {
			yield 1;
			yield 2;
		};

		$this
			->assert('Use all yields then return')
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

			->exception(function() use ($asserter) {
				$asserter->returns;
			})
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('The returns asserter could only be used with PHP>=7.0')
		;
	}



	/**
	 * @php >= 7.0
	 */
	public function testReturns()
	{
		if (version_compare(PHP_VERSION, '7.0') >= 0) {
			$generator = eval(<<<'PHP'
return function() {
    for ($i=0; $i<2; $i++) {
        yield ($i+1);
    }

    return 42;
};
PHP
			);
		}

		$this
			->assert('Use all yields then return')
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

				->when($yieldAsserter = $asserter->returns)
					->object($yieldAsserter)->isInstanceOf('\mageekguy\atoum\asserters\variable')
					->integer($yieldAsserter->getValue())->isEqualTo(42)

			->assert('Use return before all yields')
				->given($asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
				)
				->then
					->object($asserter->setWith($generator()))->isIdenticalTo($asserter)

				->when($yieldAsserter = $asserter->yields)
					->object($yieldAsserter)->isInstanceOf('\mageekguy\atoum\asserters\variable')
					->integer($yieldAsserter->getValue())->isEqualTo(1)


				->exception(
					function() use($asserter) {
						$asserter->returns;
					}
				)
					->isInstanceOf('\Exception')
					->hasMessage("Cannot get return value of a generator that hasn't returned")
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
