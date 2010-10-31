<?php

namespace mageekguy\atoum\tests\units\mock;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;

require_once(__DIR__ . '/../../runner.php');

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

	public function testOverload()
	{
		$generator = new mock\generator();

		$this->assert
			->object($generator->overload(new mock\php\method(uniqid())))->isIdenticalTo($generator)
		;
	}

	public function testShunt()
	{
		$generator = new mock\generator();

		$this->assert
			->object($generator->shunt(uniqid()))->isIdenticalTo($generator)
		;
	}

	public function testGetMockedClassCode()
	{
		$realClass = uniqid();

		$adapter = new atoum\adapter();

		$adapter->class_exists = function($class) use ($realClass) { return ($class == '\\' . $realClass); };

		$generator = new mock\generator($adapter);

		$mockGenerator = new mock\generator();

		$mockGenerator->generate('\reflectionMethod');

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = function() { return '__construct'; };
		$reflectionMethodController->isConstructor = function() { return true; };
		$reflectionMethodController->getParameters = function() { return array(); };
		$reflectionMethodController->isConstructor = function() { return true; };
		$reflectionMethodController->isFinal = function() { return false; };
		$reflectionMethodController->isStatic = function() { return false; };
		$reflectionMethodController->returnsReference = function() { return false; };
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new mock\reflectionMethod(null, null);

		$mockGenerator->generate('\reflectionClass');

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use ($realClass) { return $realClass; };
		$reflectionClassController->isFinal = function() { return false; };
		$reflectionClassController->getMethods = function() use ($reflectionMethod) { return array($reflectionMethod); };
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new mock\reflectionClass(null);

		$generator->setReflectionClassInjecter(function($class) use ($reflectionClass) { return $reflectionClass; });

		$this->assert
			->string($generator->getMockedClassCode($realClass))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . "\n" .
				'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\mock\aggregator' . "\n" .
				'{' . "\n" .
				"\t" . 'private $mockController = null;' . "\n" .
				"\t" . 'public function getMockController()' . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($this->mockController === null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->setMockController(new \mageekguy\atoum\mock\controller());' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'return $this->mockController;' . "\n" .
				"\t" . '}' . "\n" .
				"\t" . 'public function setMockController(\mageekguy\atoum\mock\controller $controller)' . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($this->mockController !== $controller)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->mockController = $controller->control($this);' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'return $this->mockController;' . "\n" .
				"\t" . '}' . "\n" .
				"\t" . 'public function resetMockController()' . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($this->mockController !== null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$mockController = $this->mockController;' . "\n" .
				"\t\t\t" . '$this->mockController = null;' . "\n" .
				"\t\t\t" . '$mockController->reset();' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'return $this;' . "\n" .
				"\t" . '}' . "\n" .
				"\t" . 'public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($mockController === null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'if ($mockController !== null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->setMockController($mockController);' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'if ($this->mockController !== null && isset($this->mockController->__construct) === true)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->mockController->invoke(\'__construct\', array());' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'else' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . 'parent::__construct();' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t" . '}' . "\n" .
				'}' . "\n" .
				'}'
			)
		;

		$overloadedMethod = new mock\php\method('__construct');
		$overloadedMethod->addArgument($argument = new mock\php\method\argument(uniqid()));

		$generator->overload($overloadedMethod);

		$this->assert
			->string($generator->getMockedClassCode($realClass))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . "\n" .
				'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\mock\aggregator' . "\n" .
				'{' . "\n" .
				"\t" . 'private $mockController = null;' . "\n" .
				"\t" . 'public function getMockController()' . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($this->mockController === null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->setMockController(new \mageekguy\atoum\mock\controller());' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'return $this->mockController;' . "\n" .
				"\t" . '}' . "\n" .
				"\t" . 'public function setMockController(\mageekguy\atoum\mock\controller $controller)' . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($this->mockController !== $controller)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->mockController = $controller->control($this);' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'return $this->mockController;' . "\n" .
				"\t" . '}' . "\n" .
				"\t" . 'public function resetMockController()' . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($this->mockController !== null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$mockController = $this->mockController;' . "\n" .
				"\t\t\t" . '$this->mockController = null;' . "\n" .
				"\t\t\t" . '$mockController->reset();' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'return $this;' . "\n" .
				"\t" . '}' . "\n" .
				"\t" . $overloadedMethod . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($mockController === null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'if ($mockController !== null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->setMockController($mockController);' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'if ($this->mockController !== null && isset($this->mockController->__construct) === true)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->mockController->invoke(\'__construct\', array(' . $argument->getVariable() . '));' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'else' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . 'parent::__construct(' . $argument->getVariable() . ');' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t" . '}' . "\n" .
				'}' . "\n" .
				'}'
			)
		;

		$generator->shunt('__construct');

		$this->assert
			->string($generator->getMockedClassCode($realClass))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . "\n" .
				'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\mock\aggregator' . "\n" .
				'{' . "\n" .
				"\t" . 'private $mockController = null;' . "\n" .
				"\t" . 'public function getMockController()' . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($this->mockController === null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->setMockController(new \mageekguy\atoum\mock\controller());' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'return $this->mockController;' . "\n" .
				"\t" . '}' . "\n" .
				"\t" . 'public function setMockController(\mageekguy\atoum\mock\controller $controller)' . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($this->mockController !== $controller)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->mockController = $controller->control($this);' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'return $this->mockController;' . "\n" .
				"\t" . '}' . "\n" .
				"\t" . 'public function resetMockController()' . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($this->mockController !== null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$mockController = $this->mockController;' . "\n" .
				"\t\t\t" . '$this->mockController = null;' . "\n" .
				"\t\t\t" . '$mockController->reset();' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'return $this;' . "\n" .
				"\t" . '}' . "\n" .
				"\t" . 'public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . "\n" .
				"\t" . '{' . "\n" .
				"\t\t" . 'if ($mockController === null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . "\n" .
				"\t\t\t" . 'if ($mockController === null)' . "\n" .
				"\t\t\t" . '{' . "\n" .
				"\t\t\t\t" . '$mockController = new \mageekguy\atoum\mock\controller();' . "\n" .
				"\t\t\t" . '}' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'if ($mockController !== null)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->setMockController($mockController);' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . 'if (isset($this->mockController->__construct) === false)' . "\n" .
				"\t\t" . '{' . "\n" .
				"\t\t\t" . '$this->mockController->__construct = function() {};' . "\n" .
				"\t\t" . '}' . "\n" .
				"\t\t" . '$this->mockController->invoke(\'__construct\', array());' . "\n" .
				"\t" . '}' . "\n" .
				'}' . "\n" .
				'}'
			)
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

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->isFinal = function() { return true; };

		$reflectionClass = new atoum\mock\reflectionClass(uniqid(), $reflectionClassController);

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

		$reflectionClassController->isFinal = function() { return false; };

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
