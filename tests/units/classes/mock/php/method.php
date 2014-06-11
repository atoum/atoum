<?php

namespace mageekguy\atoum\tests\units\mock\php;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\php
;

require_once __DIR__ . '/../../../runner.php';

class method extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($this->newTestedInstance($name = uniqid()))
			->then
				->string($this->testedInstance->getName())->isEqualTo($name)
		;
	}

	public function testReturnReference()
	{
		$this
			->if($this->newTestedInstance(uniqid()))
			->then
				->object($this->testedInstance->returnReference())->isTestedInstance
			->if($method = $this->newTestedInstance('__construct'))
			->then
				->exception(function() use ($method) {
							$method->returnReference();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Constructor can not return a reference')
		;
	}

	public function testIsConstructor()
	{
		$this
			->if($this->newTestedInstance(uniqid()))
			->then
				->boolean($this->testedInstance->isConstructor())->isFalse()
			->if($this->newTestedInstance('__construct'))
			->then
				->boolean($this->testedInstance->isConstructor())->isTrue()
		;
	}

	public function testAddArgument()
	{
		$this
			->if($this->newTestedInstance(uniqid()))
			->then
				->object($this->testedInstance->addArgument(new php\method\argument(uniqid())))->isTestedInstance
		;
	}

	public function test__toString()
	{
		$this
			->if($this->newTestedInstance($name = uniqid()))
			->then
				->castToString($this->testedInstance)->isEqualTo('public function ' . $name . '()')
				->castToString($this->testedInstance->returnReference())->isEqualTo('public function & ' . $name . '()')
				->castToString($this->testedInstance->addArgument($argument1 = new php\method\argument(uniqid())))->isEqualTo('public function & ' . $name . '(' . $argument1 . ')')
				->castToString($this->testedInstance->addArgument($argument2 = new php\method\argument(uniqid())))->isEqualTo('public function & ' . $name . '(' . $argument1 . ', ' . $argument2 . ')')
		;
	}

	public function testGetArgumentsAsString()
	{
		$this
			->if($this->newTestedInstance($name = uniqid()))
			->then
				->string($this->testedInstance->getArgumentsAsString())->isEmpty()
				->string($this->testedInstance->addArgument($argument1 = new php\method\argument(uniqid()))->getArgumentsAsString())->isEqualTo((string) $argument1)
				->string($this->testedInstance->addArgument($argument2 = new php\method\argument(uniqid()))->getArgumentsAsString())->isEqualTo($argument1 . ', ' . $argument2)
				->string($this->testedInstance->addArgument($argument3 = new php\method\argument(uniqid()))->getArgumentsAsString())->isEqualTo($argument1 . ', ' . $argument2 . ', ' . $argument3)
		;
	}
}
