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
		$method = new php\method($name = uniqid());

		$this->assert
			->string($method->getName())->isEqualTo($name)
		;
	}

	public function testReturnReference()
	{
		$method = new php\method(uniqid());

		$this->assert
			->object($method->returnReference())->isIdenticalTo($method)
		;

		$method = new php\method('__construct');

		$this->assert
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
		$method = new php\method(uniqid());

		$this->assert
			->boolean($method->isConstructor())->isFalse()
		;

		$method = new php\method('__construct');

		$this->assert
			->boolean($method->isConstructor())->isTrue()
		;
	}

	public function testAddArgument()
	{
		$method = new php\method(uniqid());

		$this->assert
			->object($method->addArgument(new php\method\argument(uniqid())))->isIdenticalTo($method)
		;
	}

	public function test__toString()
	{
		$method = new php\method($name = uniqid());

		$this->assert
			->castToString($method)->isEqualTo('public function ' . $name . '()')
			->castToString($method->returnReference())->isEqualTo('public function & ' . $name . '()')
			->castToString($method->addArgument($argument1 = new php\method\argument(uniqid())))->isEqualTo('public function & ' . $name . '(' . $argument1 . ')')
			->castToString($method->addArgument($argument2 = new php\method\argument(uniqid())))->isEqualTo('public function & ' . $name . '(' . $argument1 . ', ' . $argument2 . ')')
		;
	}

	public function testGetArgumentsAsString()
	{
		$method = new php\method($name = uniqid());

		$this->assert
			->string($method->getArgumentsAsString())->isEmpty()
			->string($method->addArgument($argument1 = new php\method\argument(uniqid()))->getArgumentsAsString())->isEqualTo((string) $argument1)
			->string($method->addArgument($argument2 = new php\method\argument(uniqid()))->getArgumentsAsString())->isEqualTo($argument1 . ', ' . $argument2)
			->string($method->addArgument($argument3 = new php\method\argument(uniqid()))->getArgumentsAsString())->isEqualTo($argument1 . ', ' . $argument2 . ', ' . $argument3)
		;
	}
}
