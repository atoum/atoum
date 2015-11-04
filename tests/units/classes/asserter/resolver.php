<?php

namespace mageekguy\atoum\tests\units\asserter;

require __DIR__ . '/../../runner.php';

use
	atoum
;

class resolver extends atoum
{
	public function test__construct()
	{
		$this
			->given($this->newTestedInstance())
			->then
				->string($this->testedInstance->getBaseClass())->isEqualTo('mageekguy\atoum\asserter')
				->array($this->testedInstance->getNamespaces())->isEqualTo(array('mageekguy\atoum\asserters'))

			->given($this->newTestedInstance($baseClass = uniqid(), $namespace = uniqid()))
			->then
				->string($this->testedInstance->getBaseClass())->isEqualTo($baseClass)
				->array($this->testedInstance->getNamespaces())->isEqualTo(array($namespace))
		;
	}

	public function testAddNamespace()
	{
		$this
			->given($this->newTestedInstance())
			->then
				->object($this->testedInstance->addNamespace($namespace1 = uniqid()))->isTestedInstance
				->array($this->testedInstance->getNamespaces())->isEqualTo(array('mageekguy\atoum\asserters', $namespace1))

				->object($this->testedInstance->addNamespace(($namespace2 = uniqid()) . '\\'))->isTestedInstance
				->array($this->testedInstance->getNamespaces())->isEqualTo(array('mageekguy\atoum\asserters', $namespace1, $namespace2))

				->object($this->testedInstance->addNamespace('\\' . ($namespace3 = uniqid()) . '\\'))->isTestedInstance
				->array($this->testedInstance->getNamespaces())->isEqualTo(array('mageekguy\atoum\asserters', $namespace1, $namespace2, $namespace3))

				->object($this->testedInstance->addNamespace('\\' . ($namespace4 = uniqid())))->isTestedInstance
				->array($this->testedInstance->getNamespaces())->isEqualTo(array('mageekguy\atoum\asserters', $namespace1, $namespace2, $namespace3, $namespace4))
		;
	}

	public function testResolve()
	{
		$this
			->given($this->newTestedInstance())
			->then
				->if(
					$this->function->class_exists = true,
					$this->function->is_subclass_of = true
				)
				->then
					->string($this->testedInstance->resolve($asserter = uniqid('a')))->isEqualTo('mageekguy\atoum\asserters\\' . $asserter)
					->string($this->testedInstance->resolve($asserter = '\\' . uniqid('a')))->isEqualTo($asserter)
					->string($this->testedInstance->resolve($asserter = uniqid('a') . '\\' . uniqid('a')))->isEqualTo($asserter)
					->string($this->testedInstance->resolve($asserter = '\\' . uniqid('a') . '\\' . uniqid('a')))->isEqualTo($asserter)
					->variable($this->testedInstance->resolve(uniqid(1)))->isNull
					->variable($this->testedInstance->resolve('\\' . uniqid(1)))->isNull
					->variable($this->testedInstance->resolve(uniqid(1) . '\\' . $asserter))->isNull
					->variable($this->testedInstance->resolve(uniqid(1) . '\\' . uniqid(2)))->isNull
				->if($this->function->class_exists = function($class) use (& $unknownClass) { return ($class !== 'mageekguy\atoum\asserters\\' . $unknownClass); })
				->then
					->variable($this->testedInstance->resolve($unknownClass = uniqid()))->isNull
		;
	}
}
