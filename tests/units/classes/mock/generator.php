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

	public function testSetReflectionClassInjector()
	{
		$generator = new mock\generator();


		$this->assert
			->exception(function() use ($generator) {
					$generator->setReflectionClassInjector(function() {});
				}
			)
				->isInstanceOf('\runtimeException')
				->hasMessage('Reflection class injector must take one argument')
		;

		$reflectionClass = new \reflectionClass($this);

		$this->assert
			->object($generator->getReflectionClass(__CLASS__))->isInstanceOf('\reflectionClass')
			->object($generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; }))->isIdenticalTo($generator)
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
		$adapter = new atoum\adapter();

		$adapter->class_exists = function() { return false; };

		$generator = new mock\generator($adapter);

		$this->assert
			->string($generator->getMockedClassCode($unknownClass = uniqid()))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . PHP_EOL .
				'final class ' . $unknownClass . ' implements \mageekguy\atoum\mock\aggregator' . PHP_EOL .
				'{' . PHP_EOL .
				'	private $mockController = null;' . PHP_EOL .
				'	public function getMockController()' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController === null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->setMockController(new \mageekguy\atoum\mock\controller());' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this->mockController;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController !== $controller)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->mockController = $controller->control($this);' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this->mockController;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function resetMockController()' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController !== null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$mockController = $this->mockController;' . PHP_EOL .
				'			$this->mockController = null;' . PHP_EOL .
				'			$mockController->reset();' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($mockController === null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
				'		}' . PHP_EOL .
				'		if ($mockController === null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$mockController = new \mageekguy\atoum\mock\controller();' . PHP_EOL .
				'		}' . PHP_EOL .
				'		if ($mockController !== null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->setMockController($mockController);' . PHP_EOL .
				'		}' . PHP_EOL .
				'		if ($this->mockController !== null && isset($this->mockController->__construct) === true)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
				'		}' . PHP_EOL .
				'	}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;

		$realClass = uniqid();

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
		$reflectionClassController->isInterface = function() { return false; };
		$reflectionClassController->getMethods = function() use ($reflectionMethod) { return array($reflectionMethod); };
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new mock\reflectionClass(null);

		$generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; });

		$this->assert
			->string($generator->getMockedClassCode($realClass))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . PHP_EOL .
				'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\mock\aggregator' . PHP_EOL .
				'{' . PHP_EOL .
				'	private $mockController = null;' . PHP_EOL .
				'	public function getMockController()' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController === null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->setMockController(new \mageekguy\atoum\mock\controller());' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this->mockController;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController !== $controller)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->mockController = $controller->control($this);' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this->mockController;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function resetMockController()' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController !== null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$mockController = $this->mockController;' . PHP_EOL .
				'			$this->mockController = null;' . PHP_EOL .
				'			$mockController->reset();' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($mockController === null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
				'		}' . PHP_EOL .
				'		if ($mockController !== null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->setMockController($mockController);' . PHP_EOL .
				'		}' . PHP_EOL .
				'		if ($this->mockController !== null && isset($this->mockController->__construct) === true)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
				'		}' . PHP_EOL .
				'		else' . PHP_EOL .
				'		{' . PHP_EOL .
				'			parent::__construct();' . PHP_EOL .
				'		}' . PHP_EOL .
				'	}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;

		$overloadedMethod = new mock\php\method('__construct');
		$overloadedMethod->addArgument($argument = new mock\php\method\argument(uniqid()));

		$generator->overload($overloadedMethod);

		$this->assert
			->string($generator->getMockedClassCode($realClass))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . PHP_EOL .
				'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\mock\aggregator' . PHP_EOL .
				'{' . PHP_EOL .
				'	private $mockController = null;' . PHP_EOL .
				'	public function getMockController()' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController === null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->setMockController(new \mageekguy\atoum\mock\controller());' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this->mockController;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController !== $controller)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->mockController = $controller->control($this);' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this->mockController;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function resetMockController()' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController !== null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$mockController = $this->mockController;' . PHP_EOL .
				'			$this->mockController = null;' . PHP_EOL .
				'			$mockController->reset();' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	' . $overloadedMethod . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($mockController === null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
				'		}' . PHP_EOL .
				'		if ($mockController !== null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->setMockController($mockController);' . PHP_EOL .
				'		}' . PHP_EOL .
				'		if ($this->mockController !== null && isset($this->mockController->__construct) === true)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->mockController->invoke(\'__construct\', array(' . $argument->getVariable() . '));' . PHP_EOL .
				'		}' . PHP_EOL .
				'		else' . PHP_EOL .
				'		{' . PHP_EOL .
				'			parent::__construct(' . $argument->getVariable() . ');' . PHP_EOL .
				'		}' . PHP_EOL .
				'	}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;

		$generator->shunt('__construct');

		$this->assert
			->string($generator->getMockedClassCode($realClass))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . PHP_EOL .
				'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\mock\aggregator' . PHP_EOL .
				'{' . PHP_EOL .
				'	private $mockController = null;' . PHP_EOL .
				'	public function getMockController()' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController === null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->setMockController(new \mageekguy\atoum\mock\controller());' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this->mockController;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController !== $controller)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->mockController = $controller->control($this);' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this->mockController;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function resetMockController()' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController !== null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$mockController = $this->mockController;' . PHP_EOL .
				'			$this->mockController = null;' . PHP_EOL .
				'			$mockController->reset();' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($mockController === null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
				'			if ($mockController === null)' . PHP_EOL .
				'			{' . PHP_EOL .
				'				$mockController = new \mageekguy\atoum\mock\controller();' . PHP_EOL .
				'			}' . PHP_EOL .
				'		}' . PHP_EOL .
				'		if ($mockController !== null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->setMockController($mockController);' . PHP_EOL .
				'		}' . PHP_EOL .
				'		if (isset($this->mockController->__construct) === false)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->mockController->__construct = function() {};' . PHP_EOL .
				'		}' . PHP_EOL .
				'		$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
				'	}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;

		$reflectionClassController->isInterface = function() { return true; };

		$this->assert
			->string($generator->getMockedClassCode($realClass))->isEqualTo(
				'namespace mageekguy\atoum\mock {' . PHP_EOL .
				'final class ' . $realClass . ' implements \\' . $realClass . ', \mageekguy\atoum\mock\aggregator' . PHP_EOL .
				'{' . PHP_EOL .
				'	private $mockController = null;' . PHP_EOL .
				'	public function getMockController()' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController === null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->setMockController(new \mageekguy\atoum\mock\controller());' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this->mockController;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController !== $controller)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->mockController = $controller->control($this);' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this->mockController;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function resetMockController()' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($this->mockController !== null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$mockController = $this->mockController;' . PHP_EOL .
				'			$this->mockController = null;' . PHP_EOL .
				'			$mockController->reset();' . PHP_EOL .
				'		}' . PHP_EOL .
				'		return $this;' . PHP_EOL .
				'	}' . PHP_EOL .
				'	public function __construct(\mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
				'	{' . PHP_EOL .
				'		if ($mockController === null)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
				'			if ($mockController === null)' . PHP_EOL .
				'			{' . PHP_EOL .
				'				$mockController = new \mageekguy\atoum\mock\controller();' . PHP_EOL .
				'			}' . PHP_EOL .
				'		}' . PHP_EOL .
				'		$this->setMockController($mockController);' . PHP_EOL .
				'		if (isset($this->mockController->__construct) === false)' . PHP_EOL .
				'		{' . PHP_EOL .
				'			$this->mockController->__construct = function() {};' . PHP_EOL .
				'		}' . PHP_EOL .
				'		$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
				'	}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;
	}

	public function testGenerate()
	{
		$adapter = new atoum\adapter();

		$generator = new mock\generator($adapter);

		$adapter->class_exists = function() { return false; };
		$adapter->interface_exists = function() { return false; };

		$class = uniqid('unknownClass');

		$this->assert
			->object($generator->generate($class))->isIdenticalTo($generator)
			->class('\mageekguy\atoum\mock\\' . $class)
				->hasInterface('\mageekguy\atoum\mock\aggregator')
		;

		$class = '\\' . uniqid('unknownClass');

		$this->assert
			->object($generator->generate($class))->isIdenticalTo($generator)
			->class('\mageekguy\atoum\mock' . $class)
				->hasInterface('\mageekguy\atoum\mock\aggregator')
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
		$reflectionClassController->isInterface = function() { return false; };

		$reflectionClass = new atoum\mock\reflectionClass(uniqid(), $reflectionClassController);

		$generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; });

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
