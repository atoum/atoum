<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

require_once(__DIR__ . '/../../runner.php');

class generator extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->hasInterface('mageekguy\atoum\adapter\aggregator')
		;
	}

	public function test__construct()
	{
		$generator = new mock\generator();

		$this->assert
			->object($generator->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
		;

		$adapter = new atoum\test\adapter();

		$generator = new mock\generator($adapter);

		$this->assert
			->object($generator->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetDefaultNamespace()
	{
		$generator = new mock\generator();

		$this->assert
			->object($generator->setDefaultNamespace($namespace = uniqid()))->isIdenticalTo($generator)
			->string($generator->getDefaulNamespace())->isEqualTo('\\' . $namespace)
			->object($generator->setDefaultNamespace('\\' . $namespace))->isIdenticalTo($generator)
			->string($generator->getDefaulNamespace())->isEqualTo('\\' . $namespace)
			->object($generator->setDefaultNamespace('\\' . $namespace . '\\'))->isIdenticalTo($generator)
			->string($generator->getDefaulNamespace())->isEqualTo('\\' . $namespace)
			->object($generator->setDefaultNamespace($namespace . '\\'))->isIdenticalTo($generator)
			->string($generator->getDefaulNamespace())->isEqualTo('\\' . $namespace)
		;
	}

	public function testSetAdapter()
	{
		$generator = new mock\generator();

		$this->assert
			->object($generator->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($generator)
			->object($generator->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetReflectionClassInjector()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->shunt('__construct')->generate('reflectionClass');

		$this->assert
			->object($mockGenerator->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new \mock\reflectionClass($class)); }))->isIdenticalTo($mockGenerator)
			->object($mockGenerator->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
			->exception(function() use ($mockGenerator) {
					$mockGenerator->setReflectionClassInjector(function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Reflection class injector must take one argument')
		;
	}

	public function testGetReflectionClass()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->shunt('__construct')->generate('reflectionClass');

		$this->assert
			->object($mockGenerator->getReflectionClass(__CLASS__))->isInstanceOf('reflectionClass')
			->string($mockGenerator->getReflectionClass(__CLASS__)->getName())->isEqualTo(__CLASS__)
		;

		$mockGenerator->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new \mock\reflectionClass($class)); });

		$this->assert
			->object($mockGenerator->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
			->mock($reflectionClass)->call('__construct')->withArguments($class)->once()
		;

		$mockGenerator->setReflectionClassInjector(function($class) use (& $reflectionClass) { return uniqid(); });

		$this->assert
			->exception(function() use ($mockGenerator) {
						$mockGenerator->getReflectionClass(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime\unexpectedValue')
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
			->object($generator->shunt($method = uniqid()))->isIdenticalTo($generator)
			->boolean($generator->isShunted($method))->isTrue()
			->boolean($generator->isShunted(strtoupper($method)))->isTrue()
			->boolean($generator->isShunted(strtolower($method)))->isTrue()
			->boolean($generator->isShunted(uniqid()))->isFalse()
		;
	}

	public function testGetMockedClassCodeForUnknownClass()
	{
		$adapter = new atoum\test\adapter();

		$adapter->class_exists = false;

		$generator = new mock\generator($adapter);

		$this->assert
			->string($generator->getMockedClassCode($unknownClass = uniqid()))->isEqualTo(
				'namespace mock {' . PHP_EOL .
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
				"\t\t" . '$this->getMockController()->disableMethodChecking();' . PHP_EOL .
				"\t\t" . 'if (isset($this->getMockController()->__construct) === true)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->mockController->invoke(\'__construct\', array());' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
				"\t" . '{' . PHP_EOL .
				"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . 'return $this->mockController->invoke($methodName, $arguments);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t\t" . 'else' . PHP_EOL .
				"\t\t" . '{' . PHP_EOL .
				"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;

		$unknownClass = __NAMESPACE__ . '\dummy';

		$generator->generate($unknownClass);

		$mockedUnknownClass = '\mock\\' . $unknownClass;

		$dummy = new $mockedUnknownClass();

		$dummy->bar();

		$this->assert
			->array($dummy->getMockController()->getCalls('bar'))->hasSize(1)
		;

		$dummy->bar();

		$this->assert
			->array($dummy->getMockController()->getCalls('bar'))->hasSize(2)
		;
	}

	public function testGetMockedClassCodeForRealClass()
	{
		$this->mockGenerator
			->generate('reflectionMethod')
			->generate('reflectionClass')
		;

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = '__construct';
		$reflectionMethodController->isConstructor = true;
		$reflectionMethodController->getParameters = array();
		$reflectionMethodController->isPublic = true;
		$reflectionMethodController->isFinal = false;
		$reflectionMethodController->isStatic = false;
		$reflectionMethodController->isAbstract = false;
		$reflectionMethodController->returnsReference = false;
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new \mock\reflectionMethod(null, null);

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use (& $realClass) { return $realClass; };
		$reflectionClassController->isFinal = false;
		$reflectionClassController->isInterface = false;
		$reflectionClassController->getMethods = array($reflectionMethod);
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new \mock\reflectionClass(null);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); };

		$generator = new mock\generator($adapter);
		$generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; });

		$this->assert
			->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
				'namespace mock {' . PHP_EOL .
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
		$this->mockGenerator
			->generate('reflectionMethod')
			->generate('reflectionClass')
		;

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = '__construct';
		$reflectionMethodController->isConstructor = true;
		$reflectionMethodController->getParameters = array();
		$reflectionMethodController->isPublic = true;
		$reflectionMethodController->isFinal = false;
		$reflectionMethodController->isAbstract = false;
		$reflectionMethodController->isStatic = false;
		$reflectionMethodController->returnsReference = false;
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new \mock\reflectionMethod(null, null);

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use (& $realClass) { return $realClass; };
		$reflectionClassController->isFinal = false;
		$reflectionClassController->isInterface = false;
		$reflectionClassController->getMethods = array($reflectionMethod);
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new \mock\reflectionClass(null);

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
				'namespace mock {' . PHP_EOL .
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
				"\t\t\t" . 'parent::__construct(' . $argument->getVariable() . ');' . PHP_EOL .
				"\t\t" . '}' . PHP_EOL .
				"\t" . '}' . PHP_EOL .
				'}' . PHP_EOL .
				'}'
			)
		;
	}

	public function testGetMockedClassCodeWithAbstractMethod()
	{
		$this->mockGenerator
			->generate('reflectionMethod')
			->generate('reflectionClass')
		;

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = function() { return '__construct'; };
		$reflectionMethodController->isConstructor = true;
		$reflectionMethodController->getParameters = array();
		$reflectionMethodController->isPublic = true;
		$reflectionMethodController->isFinal = false;
		$reflectionMethodController->isStatic = false;
		$reflectionMethodController->isAbstract = true;
		$reflectionMethodController->returnsReference = false;
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new \mock\reflectionMethod(null, null);

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use (& $realClass) { return $realClass; };
		$reflectionClassController->isFinal = false;
		$reflectionClassController->isInterface = false;
		$reflectionClassController->getMethods = array($reflectionMethod);
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new \mock\reflectionClass(null);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); };

		$generator = new mock\generator($adapter);
		$generator
			->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
		;

		$this->assert
			->string($generator->getMockedClassCode($realClass))->isEqualTo(
				'namespace mock {' . PHP_EOL .
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

	public function testGetMockedClassCodeWithShuntedMethod()
	{
		$this->mockGenerator
			->generate('reflectionMethod')
			->generate('reflectionClass')
		;

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = function() { return '__construct'; };
		$reflectionMethodController->isConstructor = true;
		$reflectionMethodController->getParameters = array();
		$reflectionMethodController->isPublic = true;
		$reflectionMethodController->isFinal = false;
		$reflectionMethodController->isStatic = false;
		$reflectionMethodController->returnsReference = false;
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new \mock\reflectionMethod(null, null);

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use (& $realClass) { return $realClass; };
		$reflectionClassController->isFinal = false;
		$reflectionClassController->isInterface = false;
		$reflectionClassController->getMethods = array($reflectionMethod);
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new \mock\reflectionClass(null);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); };

		$generator = new mock\generator($adapter);
		$generator
			->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
			->shunt('__construct')
		;

		$this->assert
			->string($generator->getMockedClassCode($realClass))->isEqualTo(
				'namespace mock {' . PHP_EOL .
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
		$this->mockGenerator
			->generate('reflectionMethod')
			->generate('reflectionClass')
		;

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = function() { return '__construct'; };
		$reflectionMethodController->isConstructor = true;
		$reflectionMethodController->getParameters = array();
		$reflectionMethodController->isFinal = false;
		$reflectionMethodController->isStatic = false;
		$reflectionMethodController->returnsReference = false;
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new \mock\reflectionMethod(null, null);

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use (& $realClass) { return $realClass; };
		$reflectionClassController->isFinal = false;
		$reflectionClassController->isInterface = true;
		$reflectionClassController->getMethods = array($reflectionMethod);
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new \mock\reflectionClass(null);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); };

		$generator = new mock\generator($adapter);
		$generator
			->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
		;

		$this->assert
			->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
				'namespace mock {' . PHP_EOL .
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
		$this->mockGenerator
			->generate('reflectionMethod')
			->generate('reflectionClass')
		;

		$reflectionMethodController = new mock\controller();
		$reflectionMethodController->__construct = function() {};
		$reflectionMethodController->getName = $methodName = uniqid();
		$reflectionMethodController->isConstructor = false;
		$reflectionMethodController->getParameters = array();
		$reflectionMethodController->isPublic = true;
		$reflectionMethodController->isFinal = false;
		$reflectionMethodController->isAbstract = false;
		$reflectionMethodController->isStatic = false;
		$reflectionMethodController->returnsReference = false;
		$reflectionMethodController->injectInNextMockInstance();

		$reflectionMethod = new \mock\reflectionMethod(null, null);

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getName = function() use (& $realClass) { return $realClass; };
		$reflectionClassController->isFinal = false;
		$reflectionClassController->isInterface = false;
		$reflectionClassController->getMethods = array($reflectionMethod);
		$reflectionClassController->injectInNextMockInstance();

		$reflectionClass = new \mock\reflectionClass(null);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); };

		$generator = new mock\generator($adapter);
		$generator
			->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
		;

		$this->assert
			->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
				'namespace mock {' . PHP_EOL .
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

		$adapter->class_exists = false;
		$adapter->interface_exists = false;

		$class = uniqid('unknownClass');

		$this->assert
			->object($generator->generate($class))->isIdenticalTo($generator)
			->class('\mock\\' . $class)
				->hasNoParent()
				->hasInterface('mageekguy\atoum\mock\aggregator')
		;

		$class = '\\' . uniqid('unknownClass');

		$this->assert
			->object($generator->generate($class))->isIdenticalTo($generator)
			->class('\mock' . $class)
				->hasNoParent()
				->hasInterface('mageekguy\atoum\mock\aggregator')
		;

		$adapter->class_exists = true;

		$class = uniqid();

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Class \'\mock\\' . $class . '\' already exists')
		;

		$class = '\\' . uniqid();

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Class \'\mock' . $class . '\' already exists')
		;

		$class = uniqid();

		$adapter->class_exists = function($arg) use ($class) { return $arg === '\\' . $class; };

		$this->mockGenerator
			->generate('reflectionClass')
		;

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->isFinal = true;
		$reflectionClassController->isInterface = false;

		$reflectionClass = new \mock\reflectionClass(uniqid(), $reflectionClassController);

		$generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; });

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Class \'\\' . $class . '\' is final, unable to mock it')
		;

		$class = '\\' . uniqid();

		$adapter->class_exists = function($arg) use ($class) { return $arg === $class; };

		$this->assert
			->exception(function () use ($generator, $class) {
					$generator->generate($class);
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Class \'' . $class . '\' is final, unable to mock it')
		;

		$reflectionClassController->isFinal = false;

		$generator = new mock\generator();

		$this->assert
			->object($generator->generate(__CLASS__))->isIdenticalTo($generator)
			->class('\mock\\' . __CLASS__)
				->hasParent(__CLASS__)
				->hasInterface('mageekguy\atoum\mock\aggregator')
		;

		$generator = new mock\generator();
		$generator->shunt('__construct');

		$this->assert
			->boolean($generator->isShunted('__construct'))->isTrue()
			->object($generator->generate('reflectionMethod'))->isIdenticalTo($generator)
			->boolean($generator->isShunted('__construct'))->isFalse()
		;

	}
}

?>
