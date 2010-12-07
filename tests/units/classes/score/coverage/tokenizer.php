<?php

namespace mageekguy\atoum\tests\units\score\coverage;

use mageekguy\atoum;
use mageekguy\atoum\mock;
use mageekguy\atoum\score\coverage;

require_once(__DIR__ . '/../../../runner.php');

class tokenizer extends atoum\test
{
	public function test__construct()
	{
		$tokenizer = new coverage\tokenizer();

		$this->assert
			->object($tokenizer->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
			->object($tokenizer->getReflectionClass(__CLASS__))->isInstanceOf('\reflectionClass')
		;

		$tokenizer = new coverage\tokenizer($adapter = new atoum\adapter());

		$this->assert
			->object($tokenizer->getAdapter())->isIdenticalTo($adapter)
			->object($tokenizer->getReflectionClass(__CLASS__))->isInstanceOf('\reflectionClass')
		;
	}

	public function testSetAdapter()
	{
		$tokenizer = new coverage\tokenizer();

		$this->assert
			->object($tokenizer->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($tokenizer)
			->object($tokenizer->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetReflectionClassInjector()
	{
		$tokenizer = new coverage\tokenizer();

		$mockGenerator = new mock\generator();
		$mockGenerator->shunt('__construct')->generate('\reflectionClass');

		$reflectionClass = new mock\reflectionClass(uniqid());

		$this->assert
			->object($tokenizer->setReflectionClassInjector(function() use ($reflectionClass) { return $reflectionClass; }))->isIdenticalTo($tokenizer)
			->object($tokenizer->getReflectionClass(uniqid()))->isIdenticalTo($reflectionClass)
		;

		$this->assert
			->object($tokenizer->setReflectionClassInjector(function() {}))->isIdenticalTo($tokenizer)
			->exception(function() use ($tokenizer) {
						$tokenizer->getReflectionClass(uniqid());
					}
				)
				->isInstanceOf('\mageekguy\atoum\score\coverage\tokenizer\exception')
				->hasMessage('Reflection class injector must return a \reflectionClass instance')
		;
	}

	public function testGetExecutableCodeFromClass()
	{
		$tokenizer = new coverage\tokenizer($adapter = new atoum\adapter());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->shunt('__construct')
				->generate('\reflectionClass')
				->generate('\reflectionMethod')
		;

		$reflectionMethod = new mock\reflectionMethod(uniqid());
		$reflectionMethod->getName = function () use (& $methodName) { return $methodName; };
		$reflectionMethod->getStartLine = function() use (& $startLine) { return $startLine; };
		$reflectionMethod->getEndLine = function() use (& $endLine) { return $endLine; };

		$reflectionClass = new mock\reflectionClass(uniqid());
		$reflectionClass->getFilename = function() {};
		$reflectionClass->getMethods = function() use ($reflectionMethod) { return array($reflectionMethod); };
	}
}

?>
