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
					->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
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

	public function testGetTestedClassName()
	{
		$coverage = new score\coverage();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\reflectionClass');

		$mockController = new mock\controller();
		$mockController->__construct = function() {};
		$mockController->getName = function() use (& $testClassName) { return $testClassName; };

		$coverage->setReflectionClassInjector(function($class) use ($mockController) { return new mock\reflectionClass($class, $mockController); });

		$className = 'name\space\foo';
		$testClassName = 'name\space\tests\units\foo';

		$this->assert
			->string($coverage->getTestedClassName($this))->isEqualTo($className)
		;

		$testClassName = 'name\space\test\unit\foo';

		$this->assert
			->variable($coverage->getTestedClassName($this))->isNull()
			->variable($coverage->getTestedClassName($this, '\test\unit'))->isEqualTo($className)
			->variable($coverage->getTestedClassName($this, 'test\unit'))->isEqualTo($className)
			->variable($coverage->getTestedClassName($this, 'test\unit\\'))->isEqualTo($className)
			->variable($coverage->getTestedClassName($this, '\test\unit\\'))->isEqualTo($className)
		;
	}

	public function testSetXdebugData()
	{
		$coverage = new score\coverage();

		$this->assert
			->object($coverage->addXdebugData($this, array()))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEmpty()
		;

		$mockController = new mock\controller();
		$mockController->__construct = function() {};
		$mockController->getName = function() use (& $testClassName) { return $testClassName; };
		$mockController->getFileName = function() use (& $classFile) { return $classFile; };

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\reflectionClass');

		$coverage->setReflectionClassInjector(function($class) use ($mockController) { return new mock\reflectionClass($class, $mockController); });

		$className = 'name\space\foo';
		$classFile = uniqid();
		$testClassName = 'name\space\tests\units\foo';

		$XdebugData = array(
		  $classFile =>
			 array(
				5 => 1,
				6 => 2,
				7 => 3,
				8 => 2,
				9 => 1
			),
		  uniqid() =>
			 array(
				5 => 2,
				6 => 3,
				7 => 4,
				8 => 3,
				9 => 2
			)
		);

		$this->assert
			->object($coverage->addXdebugData($this, $XdebugData))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEqualTo(array($classFile => array(5 => 1, 6 => 2, 7 => 3, 8 => 2, 9 => 1)))
			->object($coverage->addXdebugData($this, $XdebugData))->isIdenticalTo($coverage)
			->array($coverage->getLines())->isEqualTo(array($classFile => array(5 => 2, 6 => 4, 7 => 6, 8 => 4, 9 => 2)))
		;
	}
}

?>
