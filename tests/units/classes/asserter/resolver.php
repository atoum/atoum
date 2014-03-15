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
				->string($this->testedInstance->getNamespace())->isEqualTo('mageekguy\atoum\asserters')

			->given($this->newTestedInstance($namespace = uniqid()))
			->then
				->string($this->testedInstance->getNamespace())->isEqualTo($namespace)
		;
	}

	public function testSetNamespace()
	{
		$this
			->given($this->newTestedInstance())
			->then
				->object($this->testedInstance->setNamespace($namespace = uniqid()))->isTestedInstance
				->string($this->testedInstance->getNamespace())->isEqualTo($namespace)

				->object($this->testedInstance->setNamespace())->isTestedInstance
				->string($this->testedInstance->getNamespace())->isEqualTo('mageekguy\atoum\asserters')
		;
	}

	public function testResolve()
	{
		$this
			->given($this->newTestedInstance())
			->then
				->if($this->function->class_exists = false)
				->then
					->variable($this->testedInstance->resolve(uniqid()))->isNull

				->if($this->function->class_exists = true)
				->then
					->string($this->testedInstance->resolve($asserter = uniqid()))->isEqualTo($this->testedInstance->getNamespace() . '\\' . $asserter)

					->string($this->testedInstance->resolve($asserter = '\\' . uniqid()))->isEqualTo($asserter)
		;
	}
}
