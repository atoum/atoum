<?php

namespace mageekguy\atoum\tests\units\mock;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;

require_once(__DIR__ . '/../../runner.php');

/** @isolation off */
class generator extends atoum\test
{
	public function setUp()
	{
		$this->assert->setAlias('class', 'phpClass');
	}

	public function test__construct()
	{
		$generator = new mock\generator();

		$this->assert
			->object($generator->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
		;

		$adapter = new atoum\adapter();

		$generator = new mock\generator($adapter);

		$this->assert
			->object($generator->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetReflectionClassInjecter()
	{
		$generator = new mock\generator();


		$this->assert
			->exception(function() use ($generator) {
					$generator->setReflectionClassInjecter(function() {});
				}
			)
				->isInstanceOf('\runtimeException')
				->hasMessage('Reflection class injecter must take one argument')
		;

		$reflectionClass = new \reflectionClass($this);

		$this->assert
			->object($generator->getReflectionClass(__CLASS__))->isInstanceOf('\reflectionClass')
			->object($generator->setReflectionClassInjecter(function($class) use ($reflectionClass) { return $reflectionClass; }))->isIdenticalTo($generator)
			->object($generator->getReflectionClass(__CLASS__))->isIdenticalTo($reflectionClass)
		;
	}

	public function testGenerate()
	{
		$adapter = new atoum\adapter();

		$generator = new mock\generator($adapter);

		$adapter->class_exists = function() { return false; };

		$class = uniqid();

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Class \'\\' . $class . '\' does not exist')
		;

		$class = '\\' . uniqid();

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Class \'' . $class . '\' does not exist')
		;

		$adapter->class_exists = function() { return true; };

		$class = uniqid();

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Class \'\mageekguy\atoum\mock\\' . $class . '\' already exists')
		;

		$class = '\\' . uniqid();

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Class \'\mageekguy\atoum\mock' . $class . '\' already exists')
		;

		$class = uniqid();

		$adapter->class_exists = function($arg) use ($class) { return $arg === '\\' . $class; };

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\reflectionClass');
		$mockController = new mock\controller();
		$mockController->__construct = function() {};
		$mockController->isFinal= function() { return true; };

		$reflectionClass = new atoum\mock\reflectionClass(uniqid(), $mockController);

		$generator->setReflectionClassInjecter(function($class) use ($reflectionClass) { return $reflectionClass; });

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Class \'\\' . $class . '\' is final, unable to mock it')
		;

		$class = '\\' . uniqid();

		$adapter->class_exists = function($arg) use ($class) { return $arg === $class; };

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Class \'' . $class . '\' is final, unable to mock it')
		;

		$generator = new mock\generator();

		$this->assert
			->object($generator->generate(__CLASS__))->isIdenticalTo($generator)
			->class('\mageekguy\atoum\mock\\' . __CLASS__)
				->hasParent(__CLASS__)
				->hasInterface('\mageekguy\atoum\mock\aggregator')
		;
	}
}

?>
