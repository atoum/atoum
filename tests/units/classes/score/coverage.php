<?php

namespace mageekguy\atoum\tests\units\score;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\score;

require_once(__DIR__ . '/../../runner.php');

class coverage extends atoum\test
{
	public function test__construct()
	{
		$coverage = new score\coverage();

		$this->assert
			->array($coverage->getLines())->isEmpty()
		;
	}

	public function testSetReflectionClassInjector()
	{
		$coverage = new score\coverage();

		$mockGenerator = new mock\generator();
		$mockGenerator->shunt('__construct')->generate('\reflectionClass');

		$this->assert
			->object($coverage->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new mock\reflectionClass($class)); }))->isIdenticalTo($coverage)
			->object($coverage->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
			->mock($reflectionClass)->call('__construct', array($class))
			->exception(function() use ($coverage) {
						$coverage->setReflectionClassInjector(function() {});
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic\argument')
					->hasMessage('Reflection class injector must take one argument')
		;
	}

	public function testGetReflectionClass()
	{
		$coverage = new score\coverage();

		$this->assert
			->object($coverage->getReflectionClass(__CLASS__))->isInstanceOf('\reflectionClass')
		;

		$mockGenerator = new mock\generator();
		$mockGenerator->shunt('__construct')->generate('\reflectionClass');

		$coverage->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new mock\reflectionClass($class)); });

		$this->assert
			->object($coverage->getReflectionClass($class = uniqid()))->isInstanceOf('\mageekguy\atoum\mock\reflectionClass')
			->mock($reflectionClass)->call('__construct', array($class))
		;

		$coverage->setReflectionClassInjector(function($class) use (& $reflectionClass) { return uniqid(); });

		$this->assert
			->exception(function() use ($coverage) {
						$coverage->getReflectionClass(uniqid());
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\runtime\unexpectedValue')
					->hasMessage('Reflection class injector must return a \reflectionClass instance')
		;
	}
}

?>
