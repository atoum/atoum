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

		$adapter = new atoum\test\adapter();

		$generator = new mock\generator($adapter);

		$this->assert
			->object($generator->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetReflectionClassInjector()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->shunt('__construct')->generate('\reflectionClass');

		$this->assert
			->object($mockGenerator->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new mock\reflectionClass($class)); }))->isIdenticalTo($mockGenerator)
			->object($mockGenerator->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
			->exception(function() use ($mockGenerator) {
					$mockGenerator->setReflectionClassInjector(function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Reflection class injector must take one argument')
		;
	}

	public function testGetReflectionClass()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->shunt('__construct')->generate('\reflectionClass');

		$this->assert
			->object($mockGenerator->getReflectionClass(__CLASS__))->isInstanceOf('\reflectionClass')
			->string($mockGenerator->getReflectionClass(__CLASS__)->getName())->isEqualTo(__CLASS__)
		;

		$mockGenerator->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new mock\reflectionClass($class)); });

		$this->assert
			->object($mockGenerator->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
			->mock($reflectionClass)->call('__construct', array($class))
		;

		$mockGenerator->setReflectionClassInjector(function($class) use (& $reflectionClass) { return uniqid(); });

		$this->assert
			->exception(function() use ($mockGenerator) {
						$mockGenerator->getReflectionClass(uniqid());
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\runtime\unexpectedValue')
					->hasMessage('Reflection class injector must return a \reflectionClass instance')
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

	public function testGetMockedClassCodeForUnknownClass()
	{
		$adapter = new atoum\test\adapter();

		$adapter->class_exists = function() { return false; };

		$generator = new mock\generator($adapter);

		$this->assert
			->string($generator->getMockedClassCode($unknownClass = uniqid()))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . PHP_EOL .
				'final class ' . $unknownClass . ' implements \mageekguy\atoum\mock\aggregator' . PHP_EOL .
				'{' . PHP_EOL .
				"\t" . 'private $mockController = null;' . PHP_EOL .
				"\t" . 'public function getMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->setMockController(new \mageekguy\atoum\mock\controller());' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== $controller)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController = $controller->control($this);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function resetMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = $this->mockController;' . PHP_EOL .
				"\t\t\t" . '$this->mockController = null;' . PHP_EOL .
				"\t\t\t" . '$mockController->reset();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'if ($mockController !== null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'if (isset($this->getMockController()->__construct) === true)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;
	}

	public function testGetMockedClassCodeForRealClass()
	{
		$mockGenerator = new mock\generator();

		$mockGenerator->generate('\reflectionMethod');

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = function() { return '__construct'; };
		$reflectionMethodController->isConstructor = function() { return true; };
		$reflectionMethodController->getParameters = function() { return array(); };
		$reflectionMethodController->isPublic = function() { return true; };
		$reflectionMethodController->isFinal = function() { return false; };
		$reflectionMethodController->isStatic = function() { return false; };
		$reflectionMethodController->returnsReference = function() { return false; };
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new mock\reflectionMethod(null, null);

		$mockGenerator->generate('\reflectionClass');

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use (& $realClass) { return $realClass; };
		$reflectionClassController->isFinal = function() { return false; };
		$reflectionClassController->isInterface = function() { return false; };
		$reflectionClassController->getMethods = function() use ($reflectionMethod) { return array($reflectionMethod); };
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new mock\reflectionClass(null);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); };

		$generator = new mock\generator($adapter);
		$generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; });

		$this->assert
			->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . PHP_EOL .
				'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\mock\aggregator' . PHP_EOL .
				'{' . PHP_EOL .
				"\t" . 'private $mockController = null;' . PHP_EOL .
				"\t" . 'public function getMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->setMockController(new \mageekguy\atoum\mock\controller());' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== $controller)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController = $controller->control($this);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function resetMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = $this->mockController;' . PHP_EOL .
				"\t\t\t" . '$this->mockController = null;' . PHP_EOL .
				"\t\t\t" . '$mockController->reset();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'if ($mockController !== null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'if (isset($this->getMockController()->__construct) === true)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'else' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->getMockController()->addCall(\'__construct\', array());' . PHP_EOL .
				"\t\t\t" . 'parent::__construct();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;
	}

	public function testGetMockedClassCodeWithOverloadMethod()
	{
		$mockGenerator = new mock\generator();

		$mockGenerator->generate('\reflectionMethod');

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = function() { return '__construct'; };
		$reflectionMethodController->isConstructor = function() { return true; };
		$reflectionMethodController->getParameters = function() { return array(); };
		$reflectionMethodController->isPublic = function() { return true; };
		$reflectionMethodController->isFinal = function() { return false; };
		$reflectionMethodController->isStatic = function() { return false; };
		$reflectionMethodController->returnsReference = function() { return false; };
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new mock\reflectionMethod(null, null);

		$mockGenerator->generate('\reflectionClass');

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use (& $realClass) { return $realClass; };
		$reflectionClassController->isFinal = function() { return false; };
		$reflectionClassController->isInterface = function() { return false; };
		$reflectionClassController->getMethods = function() use ($reflectionMethod) { return array($reflectionMethod); };
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new mock\reflectionClass(null);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); };

		$overloadedMethod = new mock\php\method('__construct');
		$overloadedMethod->addArgument($argument = new mock\php\method\argument(uniqid()));

		$generator = new mock\generator($adapter);
		$generator
			->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
			->overload($overloadedMethod)
		;

		$this->assert
			->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . PHP_EOL .
				'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\mock\aggregator' . PHP_EOL .
				'{' . PHP_EOL .
				"\t" . 'private $mockController = null;' . PHP_EOL .
				"\t" . 'public function getMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->setMockController(new \mageekguy\atoum\mock\controller());' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== $controller)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController = $controller->control($this);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function resetMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = $this->mockController;' . PHP_EOL .
				"\t\t\t" . '$this->mockController = null;' . PHP_EOL .
				"\t\t\t" . '$mockController->reset();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . '' . $overloadedMethod . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'if ($mockController !== null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'if (isset($this->getMockController()->__construct) === true)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController->invoke(\'__construct\', array(' . $argument->getVariable() . '));' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'else' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->getMockController()->addCall(\'__construct\', array(' . $argument->getVariable() . '));' . PHP_EOL .
				"\t\t\t" . 'parent::__construct(' . $argument->getVariable() . ');' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;
	}

	public function testGetMockedClassCodeWithShuntedMethod()
	{
		$mockGenerator = new mock\generator();

		$mockGenerator->generate('\reflectionMethod');

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = function() { return '__construct'; };
		$reflectionMethodController->isConstructor = function() { return true; };
		$reflectionMethodController->getParameters = function() { return array(); };
		$reflectionMethodController->isPublic = function() { return true; };
		$reflectionMethodController->isFinal = function() { return false; };
		$reflectionMethodController->isStatic = function() { return false; };
		$reflectionMethodController->returnsReference = function() { return false; };
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new mock\reflectionMethod(null, null);

		$mockGenerator->generate('\reflectionClass');

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use (& $realClass) { return $realClass; };
		$reflectionClassController->isFinal = function() { return false; };
		$reflectionClassController->isInterface = function() { return false; };
		$reflectionClassController->getMethods = function() use ($reflectionMethod) { return array($reflectionMethod); };
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new mock\reflectionClass(null);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); };

		$generator = new mock\generator($adapter);
		$generator
			->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
			->shunt('__construct')
		;

		$this->assert
			->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . PHP_EOL .
				'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\mock\aggregator' . PHP_EOL .
				'{' . PHP_EOL .
				"\t" . 'private $mockController = null;' . PHP_EOL .
				"\t" . 'public function getMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->setMockController(new \mageekguy\atoum\mock\controller());' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== $controller)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController = $controller->control($this);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function resetMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = $this->mockController;' . PHP_EOL .
				"\t\t\t" . '$this->mockController = null;' . PHP_EOL .
				"\t\t\t" . '$mockController->reset();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'if ($mockController !== null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'if (isset($this->getMockController()->__construct) === false)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController->__construct = function() {};' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . '$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;
	}

	public function testGetMockedClassCodeForInterface()
	{
		$mockGenerator = new mock\generator();

		$mockGenerator->generate('\reflectionMethod');

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = function() { return '__construct'; };
		$reflectionMethodController->isConstructor = function() { return true; };
		$reflectionMethodController->getParameters = function() { return array(); };
		$reflectionMethodController->isFinal = function() { return false; };
		$reflectionMethodController->isStatic = function() { return false; };
		$reflectionMethodController->returnsReference = function() { return false; };
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new mock\reflectionMethod(null, null);

		$mockGenerator->generate('\reflectionClass');

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use (& $realClass) { return $realClass; };
		$reflectionClassController->isFinal = function() { return false; };
		$reflectionClassController->isInterface = function() { return true; };
		$reflectionClassController->getMethods = function() use ($reflectionMethod) { return array($reflectionMethod); };
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new mock\reflectionClass(null);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); };

		$generator = new mock\generator($adapter);
		$generator
			->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
		;

		$this->assert
			->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . PHP_EOL .
				'final class ' . $realClass . ' implements \\' . $realClass . ', \mageekguy\atoum\mock\aggregator' . PHP_EOL .
				'{' . PHP_EOL .
				"\t" . 'private $mockController = null;' . PHP_EOL .
				"\t" . 'public function getMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->setMockController(new \mageekguy\atoum\mock\controller());' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== $controller)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController = $controller->control($this);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function resetMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = $this->mockController;' . PHP_EOL .
				"\t\t\t" . '$this->mockController = null;' . PHP_EOL .
				"\t\t\t" . '$mockController->reset();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
				"\t\t" . 'if (isset($this->getMockController()->__construct) === false)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController->__construct = function() {};' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . '$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;
	}

	public function testGetMockedClassCodeForRealClassWithoutConstructor()
	{
		$mockGenerator = new mock\generator();

		$mockGenerator->generate('\reflectionMethod');

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = function() use (& $methodName) { return $methodName; };
		$reflectionMethodController->isConstructor = function() { return false; };
		$reflectionMethodController->getParameters = function() { return array(); };
		$reflectionMethodController->isPublic = function() { return true; };
		$reflectionMethodController->isFinal = function() { return false; };
		$reflectionMethodController->isStatic = function() { return false; };
		$reflectionMethodController->returnsReference = function() { return false; };
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new mock\reflectionMethod(null, null);

		$mockGenerator->generate('\reflectionClass');

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use (& $realClass) { return $realClass; };
		$reflectionClassController->isFinal = function() { return false; };
		$reflectionClassController->isInterface = function() { return false; };
		$reflectionClassController->getMethods = function() use ($reflectionMethod) { return array($reflectionMethod); };
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new mock\reflectionClass(null);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); };

		$generator = new mock\generator($adapter);
		$generator
			->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
		;

		$methodName = uniqid();

		$this->assert
			->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . PHP_EOL .
				'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\mock\aggregator' . PHP_EOL .
				'{' . PHP_EOL .
				"\t" . 'private $mockController = null;' . PHP_EOL .
				"\t" . 'public function getMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->setMockController(new \mageekguy\atoum\mock\controller());' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== $controller)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController = $controller->control($this);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this->mockController;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function resetMockController()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($this->mockController !== null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = $this->mockController;' . PHP_EOL .
				"\t\t\t" . '$this->mockController = null;' . PHP_EOL .
				"\t\t\t" . '$mockController->reset();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'return $this;' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function ' . $methodName . '()' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . 'return $this->mockController->invoke(\'' . $methodName . '\', array());' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'else' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', array());' . PHP_EOL .
				"\t\t\t" . 'return parent::' . $methodName . '();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if ($mockController === null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'if ($mockController !== null)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'if (isset($this->getMockController()->__construct) === true)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;
	}

	public function testGenerate()
	{
		$adapter = new atoum\test\adapter();

		$generator = new mock\generator($adapter);

		$adapter->class_exists = function() { return false; };
		$adapter->interface_exists = function() { return false; };

		$class = uniqid('unknownClass');

		$this->assert
			->object($generator->generate($class))->isIdenticalTo($generator)
			->class('\mageekguy\atoum\mock\\' . $class)
				->hasNoParent()
				->hasInterface('\mageekguy\atoum\mock\aggregator')
		;

		$class = '\\' . uniqid('unknownClass');

		$this->assert
			->object($generator->generate($class))->isIdenticalTo($generator)
			->class('\mageekguy\atoum\mock' . $class)
				->hasNoParent()
				->hasInterface('\mageekguy\atoum\mock\aggregator')
		;

		$adapter->class_exists = function() { return true; };

		$class = uniqid();

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Class \'\mageekguy\atoum\mock\\' . $class . '\' already exists')
		;

		$class = '\\' . uniqid();

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Class \'\mageekguy\atoum\mock' . $class . '\' already exists')
		;

		$class = uniqid();

		$adapter->class_exists = function($arg) use ($class) { return $arg === '\\' . $class; };

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\reflectionClass');

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->isFinal = function() { return true; };
		$reflectionClassController->isInterface = function() { return false; };

		$reflectionClass = new atoum\mock\reflectionClass(uniqid(), $reflectionClassController);

		$generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; });

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Class \'\\' . $class . '\' is final, unable to mock it')
		;

		$class = '\\' . uniqid();

		$adapter->class_exists = function($arg) use ($class) { return $arg === $class; };

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
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
