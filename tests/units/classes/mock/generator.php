<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

require_once __DIR__ . '/../../runner.php';

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
		$this->assert
			->if($generator = new mock\generator())
			->then
				->object($generator->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->if($adapter = new atoum\test\adapter())
			->and($generator = new mock\generator($adapter))
			->then
				->object($generator->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetDefaultNamespace()
	{
		$this->assert
			->if($generator = new mock\generator())
			->then
				->object($generator->setDefaultNamespace($namespace = uniqid()))->isIdenticalTo($generator)
				->string($generator->getDefaultNamespace())->isEqualTo('\\' . $namespace)
				->object($generator->setDefaultNamespace('\\' . $namespace))->isIdenticalTo($generator)
				->string($generator->getDefaultNamespace())->isEqualTo('\\' . $namespace)
				->object($generator->setDefaultNamespace('\\' . $namespace . '\\'))->isIdenticalTo($generator)
				->string($generator->getDefaultNamespace())->isEqualTo('\\' . $namespace)
				->object($generator->setDefaultNamespace($namespace . '\\'))->isIdenticalTo($generator)
				->string($generator->getDefaultNamespace())->isEqualTo('\\' . $namespace)
		;
	}

	public function testSetAdapter()
	{
		$this->assert
			->if($generator = new mock\generator())
			->then
				->object($generator->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($generator)
				->object($generator->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetReflectionClassInjector()
	{
		$this->assert
			->if($mockGenerator = new mock\generator())
			->and($mockGenerator->shunt('__construct')->generate('reflectionClass'))
			->then
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
		$this->assert
			->if($mockGenerator = new mock\generator())
			->and($mockGenerator->shunt('__construct')->generate('reflectionClass'))
			->then
				->object($mockGenerator->getReflectionClass(__CLASS__))->isInstanceOf('reflectionClass')
				->string($mockGenerator->getReflectionClass(__CLASS__)->getName())->isEqualTo(__CLASS__)
			->if($mockGenerator->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new \mock\reflectionClass($class)); }))
			->then
				->object($mockGenerator->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
				->mock($reflectionClass)->call('__construct')->withArguments($class)->once()
			->if($mockGenerator->setReflectionClassInjector(function($class) use (& $reflectionClass) { return uniqid(); }))
			->then
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
		$this->assert
			->if($generator = new mock\generator())
			->then
				->object($generator->overload(new mock\php\method(uniqid())))->isIdenticalTo($generator)
		;
	}

	public function testShunt()
	{
		$this->assert
			->if($generator = new mock\generator())
			->then
				->object($generator->shunt($method = uniqid()))->isIdenticalTo($generator)
				->boolean($generator->isShunted($method))->isTrue()
				->boolean($generator->isShunted(strtoupper($method)))->isTrue()
				->boolean($generator->isShunted(strtolower($method)))->isTrue()
				->boolean($generator->isShunted(uniqid()))->isFalse()
		;
	}

	public function testGetMockedClassCodeForUnknownClass()
	{
		$this->assert
			->if($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = false)
			->and($generator = new mock\generator($adapter))
			->then
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
			->if($unknownClass = __NAMESPACE__ . '\dummy')
			->and($generator->generate($unknownClass))
			->and($mockedUnknownClass = '\mock\\' . $unknownClass)
			->and($dummy = new $mockedUnknownClass())
			->then
				->when(function() use ($dummy) { $dummy->bar(); })
					->array($dummy->getMockController()->getCalls('bar'))->hasSize(1)
				->when(function() use ($dummy) { $dummy->bar(); })
					->array($dummy->getMockController()->getCalls('bar'))->hasSize(2)
		;
	}

	public function testGetMockedClassCodeForRealClass()
	{
		$this
			->mock('reflectionMethod')
			->mock('reflectionClass')
			->assert
				->if($reflectionMethodController = new mock\controller())
				->and($reflectionMethodController->__construct = function() {})
				->and($reflectionMethodController->getName = '__construct')
				->and($reflectionMethodController->isConstructor = true)
				->and($reflectionMethodController->getParameters = array())
				->and($reflectionMethodController->isPublic = true)
				->and($reflectionMethodController->isProtected = false)
				->and($reflectionMethodController->isFinal = false)
				->and($reflectionMethodController->isStatic = false)
				->and($reflectionMethodController->isAbstract = false)
				->and($reflectionMethodController->returnsReference = false)
				->and($reflectionMethodController->injectInNextMockInstance())
				->and($reflectionMethod = new \mock\reflectionMethod(null, null))
				->and($reflectionClassController = new mock\controller())
				->and($reflectionClassController->__construct = function() {})
				->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
				->and($reflectionClassController->isFinal = false)
				->and($reflectionClassController->isInterface = false)
				->and($reflectionClassController->getMethods = array($reflectionMethod))
				->and($reflectionClassController->injectInNextMockInstance())
				->and($reflectionClass = new \mock\reflectionClass(null))
				->and($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
				->and($generator = new mock\generator($adapter))
				->and($generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; }))
				->then
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
						"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
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
						"\t\t\t" . '$this->mockController->invoke(\'__construct\', $arguments);' . PHP_EOL .
						"\t\t" . '}' . PHP_EOL .
						"\t\t" . 'else' . PHP_EOL .
						"\t\t" . '{' . PHP_EOL .
						"\t\t\t" . 'call_user_func_array(\'parent::__construct\', $arguments);' . PHP_EOL .
						"\t\t" . '}' . PHP_EOL .
						"\t" . '}' . PHP_EOL .
						'}' . PHP_EOL .
						'}'
					)
		;
	}

	public function testGetMockedClassCodeWithOverloadMethod()
	{
		$this
			->mock('reflectionMethod')
			->mock('reflectionClass')
			->assert
				->if($reflectionMethodController = new mock\controller())
				->and($reflectionMethodController->__construct = function() {})
				->and($reflectionMethodController->getName = '__construct')
				->and($reflectionMethodController->isConstructor = true)
				->and($reflectionMethodController->getParameters = array())
				->and($reflectionMethodController->isPublic = true)
				->and($reflectionMethodController->isProtected = false)
				->and($reflectionMethodController->isFinal = false)
				->and($reflectionMethodController->isAbstract = false)
				->and($reflectionMethodController->isStatic = false)
				->and($reflectionMethodController->returnsReference = false)
				->and($reflectionMethodController->injectInNextMockInstance())
				->and($reflectionMethod = new \mock\reflectionMethod(null, null))
				->and($reflectionClassController = new mock\controller())
				->and($reflectionClassController->__construct = function() {})
				->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
				->and($reflectionClassController->isFinal = false)
				->and($reflectionClassController->isInterface = false)
				->and($reflectionClassController->getMethods = array($reflectionMethod))
				->and($reflectionClassController->injectInNextMockInstance())
				->and($reflectionClass = new \mock\reflectionClass(null))
				->and($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
				->and($overloadedMethod = new mock\php\method('__construct'))
				->and($overloadedMethod->addArgument($argument = new mock\php\method\argument(uniqid())))
				->and($generator = new mock\generator($adapter))
				->and($generator
					->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
					->overload($overloadedMethod)
				)
				->then
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
						"\t\t" . '$arguments = array_merge(array(' . $argument . '), array_slice(func_get_args(), 1, -1));' . PHP_EOL .
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
						"\t\t\t" . '$this->mockController->invoke(\'__construct\', $arguments);' . PHP_EOL .
						"\t\t" . '}' . PHP_EOL .
						"\t\t" . 'else' . PHP_EOL .
						"\t\t" . '{' . PHP_EOL .
						"\t\t\t" . 'call_user_func_array(\'parent::__construct\', $arguments);' . PHP_EOL .
						"\t\t" . '}' . PHP_EOL .
						"\t" . '}' . PHP_EOL .
						'}' . PHP_EOL .
						'}'
					)
		;
	}

	public function testGetMockedClassCodeWithAbstractMethod()
	{
		$this
			->mock('reflectionMethod')
			->mock('reflectionClass')
			->assert
				->if($reflectionMethodController = new mock\controller())
				->and($reflectionMethodController->__construct = function() {})
				->and($reflectionMethodController->getName = function() { return '__construct'; })
				->and($reflectionMethodController->isConstructor = true)
				->and($reflectionMethodController->getParameters = array())
				->and($reflectionMethodController->isPublic = true)
				->and($reflectionMethodController->isProtected = false)
				->and($reflectionMethodController->isFinal = false)
				->and($reflectionMethodController->isStatic = false)
				->and($reflectionMethodController->isAbstract = true)
				->and($reflectionMethodController->returnsReference = false)
				->and($reflectionMethodController->injectInNextMockInstance())
				->and($reflectionMethod = new \mock\reflectionMethod(null, null))
				->and($reflectionClassController = new mock\controller())
				->and($reflectionClassController->__construct = function() {})
				->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
				->and($reflectionClassController->isFinal = false)
				->and($reflectionClassController->isInterface = false)
				->and($reflectionClassController->getMethods = array($reflectionMethod))
				->and($reflectionClassController->injectInNextMockInstance())
				->and($reflectionClass = new \mock\reflectionClass(null))
				->and($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
				->and($generator = new mock\generator($adapter))
				->and($generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; }))
				->then
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
						"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
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
						"\t\t" . '$this->mockController->invoke(\'__construct\', $arguments);' . PHP_EOL .
						"\t" . '}' . PHP_EOL .
						'}' . PHP_EOL .
						'}'
					)
		;
	}

	public function testGetMockedClassCodeWithShuntedMethod()
	{
		$this
			->mock('reflectionMethod')
			->mock('reflectionClass')
			->assert
				->if($reflectionMethodController = new mock\controller())
				->and($reflectionMethodController->__construct = function() {})
				->and($reflectionMethodController->getName = function() { return '__construct'; })
				->and($reflectionMethodController->isConstructor = true)
				->and($reflectionMethodController->getParameters = array())
				->and($reflectionMethodController->isPublic = true)
				->and($reflectionMethodController->isProtected = false)
				->and($reflectionMethodController->isFinal = false)
				->and($reflectionMethodController->isStatic = false)
				->and($reflectionMethodController->returnsReference = false)
				->and($reflectionMethodController->injectInNextMockInstance())
				->and($reflectionMethod = new \mock\reflectionMethod(null, null))
				->and($reflectionClassController = new mock\controller())
				->and($reflectionClassController->__construct = function() {})
				->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
				->and($reflectionClassController->isFinal = false)
				->and($reflectionClassController->isInterface = false)
				->and($reflectionClassController->getMethods = array($reflectionMethod))
				->and($reflectionClassController->injectInNextMockInstance())
				->and($reflectionClass = new \mock\reflectionClass(null))
				->and($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
				->and($generator = new mock\generator($adapter))
				->and($generator
					->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
					->shunt('__construct')
				)
				->then
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
						"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
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
						"\t\t" . '$this->mockController->invoke(\'__construct\', $arguments);' . PHP_EOL .
						"\t" . '}' . PHP_EOL .
						'}' . PHP_EOL .
						'}'
					)
		;
	}

	public function testGetMockedClassCodeForInterface()
	{
		$this
			->mock('reflectionMethod')
			->mock('reflectionClass')
			->assert
				->if($reflectionMethodController = new mock\controller())
				->and($reflectionMethodController->__construct = function() {})
				->and($reflectionMethodController->getName = function() { return '__construct'; })
				->and($reflectionMethodController->getParameters = array())
				->and($reflectionMethodController->isFinal = false)
				->and($reflectionMethodController->isStatic = false)
				->and($reflectionMethodController->returnsReference = false)
				->and($reflectionMethodController->injectInNextMockInstance())
				->and($reflectionMethod = new \mock\reflectionMethod(null, null))
				->and($reflectionClassController = new mock\controller())
				->and($reflectionClassController->__construct = function() {})
				->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
				->and($reflectionClassController->isFinal = false)
				->and($reflectionClassController->isInterface = true)
				->and($reflectionClassController->getMethods = array($reflectionMethod))
				->and($reflectionClassController->injectInNextMockInstance())
				->and($reflectionClass = new \mock\reflectionClass(null))
				->and($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
				->and($generator = new mock\generator($adapter))
				->and($generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; }))
				->then
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
						"\t\t" . 'if ($mockController !== null)' . PHP_EOL .
						"\t\t" . '{' . PHP_EOL .
						"\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
						"\t\t" . '}' . PHP_EOL .
						"\t\t" . 'if (isset($this->getMockController()->__construct) === false)' . PHP_EOL .
						"\t\t" . '{' . PHP_EOL .
						"\t\t\t" . '$this->mockController->__construct = function() {};' . PHP_EOL .
						"\t\t" . '}' . PHP_EOL .
						"\t\t" . '$this->mockController->invoke(\'__construct\', func_get_args());' . PHP_EOL .
						"\t" . '}' . PHP_EOL .
						'}' . PHP_EOL .
						'}'
					)
		;
	}

	public function testGetMockedClassCodeForRealClassWithoutConstructor()
	{
		$this
			->mock('reflectionMethod')
			->mock('reflectionClass')
			->assert
				->if($reflectionMethodController = new mock\controller())
				->and($reflectionMethodController->__construct = function() {})
				->and($reflectionMethodController->getName = $methodName = uniqid())
				->and($reflectionMethodController->isConstructor = false)
				->and($reflectionMethodController->getParameters = array())
				->and($reflectionMethodController->isPublic = true)
				->and($reflectionMethodController->isProtected = false)
				->and($reflectionMethodController->isFinal = false)
				->and($reflectionMethodController->isAbstract = false)
				->and($reflectionMethodController->isStatic = false)
				->and($reflectionMethodController->returnsReference = false)
				->and($reflectionMethodController->injectInNextMockInstance())
				->and($reflectionMethod = new \mock\reflectionMethod(null, null))
				->and($reflectionClassController = new mock\controller())
				->and($reflectionClassController->__construct = function() {})
				->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
				->and($reflectionClassController->isFinal = false)
				->and($reflectionClassController->isInterface = false)
				->and($reflectionClassController->getMethods = array($reflectionMethod))
				->and($reflectionClassController->injectInNextMockInstance())
				->and($reflectionClass = new \mock\reflectionClass(null))
				->and($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
				->and($generator = new mock\generator($adapter))
				->and($generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; }))
				->then
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
						"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
						"\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL .
						"\t\t" . '{' . PHP_EOL .
						"\t\t\t" . 'return $this->mockController->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL .
						"\t\t" . '}' . PHP_EOL .
						"\t\t" . 'else' . PHP_EOL .
						"\t\t" . '{' . PHP_EOL .
						"\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL .
						"\t\t\t" . 'return call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL .
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
						"\t\t\t" . '$this->mockController->invoke(\'__construct\', func_get_args());' . PHP_EOL .
						"\t\t" . '}' . PHP_EOL .
						"\t" . '}' . PHP_EOL .
						'}' . PHP_EOL .
						'}'
					)
		;
	}

	public function testGetMockedClassCodeWithProtectedAbstractMethod()
	{
		$this
			->mock('reflectionMethod')
			->mock('reflectionClass')
			->assert
				->if($publicMethodController = new mock\controller())
				->and($publicMethodController->__construct = function() {})
				->and($publicMethodController->getName = $publicMethodName = uniqid())
				->and($publicMethodController->isConstructor = false)
				->and($publicMethodController->getParameters = array())
				->and($publicMethodController->isPublic = true)
				->and($publicMethodController->isProtected = false)
				->and($publicMethodController->isFinal = false)
				->and($publicMethodController->isStatic = false)
				->and($publicMethodController->isAbstract = true)
				->and($publicMethodController->returnsReference = false)
				->and($publicMethodController->injectInNextMockInstance())
				->and($publicMethod = new \mock\reflectionMethod(null, null))
				->and($protectedMethodController = new mock\controller())
				->and($protectedMethodController->__construct = function() {})
				->and($protectedMethodController->getName = $protectedMethodName = uniqid())
				->and($protectedMethodController->isConstructor = false)
				->and($protectedMethodController->getParameters = array())
				->and($protectedMethodController->isPublic = false)
				->and($protectedMethodController->isProtected = true)
				->and($protectedMethodController->isFinal = false)
				->and($protectedMethodController->isStatic = false)
				->and($protectedMethodController->isAbstract = true)
				->and($protectedMethodController->returnsReference = false)
				->and($protectedMethodController->injectInNextMockInstance())
				->and($protectedMethod = new \mock\reflectionMethod(null, null))
				->and($classlassController = new mock\controller())
				->and($classlassController->__construct = function() {})
				->and($classlassController->getName = $className = uniqid())
				->and($classlassController->isFinal = false)
				->and($classlassController->isInterface = false)
				->and($classlassController->getMethods = array($publicMethod, $protectedMethod))
				->and($classlassController->injectInNextMockInstance())
				->and($class = new \mock\reflectionClass(null))
				->and($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = function($class) use ($className) { return ($class == '\\' . $className); })
				->and($generator = new mock\generator($adapter))
				->and($generator->setReflectionClassInjector(function($class) use ($class) { return $class; }))
				->then
					->string($generator->getMockedClassCode($className))->isEqualTo(
						'namespace mock {' . PHP_EOL .
						'final class ' . $className . ' extends \\' . $className . ' implements \mageekguy\atoum\mock\aggregator' . PHP_EOL .
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
						"\t" . 'public function ' . $publicMethodName . '()' . PHP_EOL .
						"\t" . '{' . PHP_EOL .
						"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
						"\t\t" . 'if (isset($this->getMockController()->' . $publicMethodName . ') === false)' . PHP_EOL .
						"\t\t" . '{' . PHP_EOL .
						"\t\t\t" . '$this->mockController->' . $publicMethodName . ' = function() {};' . PHP_EOL .
						"\t\t" . '}' . PHP_EOL .
						"\t\t" . 'return $this->mockController->invoke(\'' . $publicMethodName . '\', $arguments);' . PHP_EOL .
						"\t" . '}' . PHP_EOL .
						"\t" . 'protected function ' . $protectedMethodName . '() {}' . PHP_EOL .
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
						"\t\t\t" . '$this->mockController->invoke(\'__construct\', func_get_args());' . PHP_EOL .
						"\t\t" . '}' . PHP_EOL .
						"\t" . '}' . PHP_EOL .
						'}' . PHP_EOL .
						'}'
					)
		;
	}

	public function testGenerate()
	{
		$this
			->mock('reflectionClass')
			->assert
				->if($adapter = new atoum\test\adapter())
				->and($generator = new mock\generator($adapter))
				->and($adapter->class_exists = false)
				->and($adapter->interface_exists = false)
				->and($class = uniqid('unknownClass'))
				->then
					->object($generator->generate($class))->isIdenticalTo($generator)
					->class('\mock\\' . $class)
						->hasNoParent()
						->hasInterface('mageekguy\atoum\mock\aggregator')
				->if($class = '\\' . uniqid('unknownClass'))
				->then
					->object($generator->generate($class))->isIdenticalTo($generator)
					->class('\mock' . $class)
						->hasNoParent()
						->hasInterface('mageekguy\atoum\mock\aggregator')
				->if($adapter->class_exists = true)
				->and($class = uniqid())
				->then
					->exception(function () use ($generator, $class) {
							$generator->generate($class);
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Class \'\mock\\' . $class . '\' already exists')
				->if($class = '\\' . uniqid())
				->then
					->exception(function () use ($generator, $class) {
							$generator->generate($class);
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Class \'\mock' . $class . '\' already exists')
				->if($class = uniqid())
				->and($adapter->class_exists = function($arg) use ($class) { return $arg === '\\' . $class; })
				->and($reflectionClassController = new mock\controller())
				->and($reflectionClassController->__construct = function() {})
				->and($reflectionClassController->isFinal = true)
				->and($reflectionClassController->isInterface = false)
				->and($reflectionClass = new \mock\reflectionClass(uniqid(), $reflectionClassController))
				->and($generator->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; }))
				->then
					->exception(function () use ($generator, $class) {
							$generator->generate($class);
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Class \'\\' . $class . '\' is final, unable to mock it')
				->if($class = '\\' . uniqid())
				->and($adapter->class_exists = function($arg) use ($class) { return $arg === $class; })
				->then
					->exception(function () use ($generator, $class) {
							$generator->generate($class);
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Class \'' . $class . '\' is final, unable to mock it')
				->if($reflectionClassController->isFinal = false)
				->and($generator = new mock\generator())
				->then
					->object($generator->generate(__CLASS__))->isIdenticalTo($generator)
					->class('\mock\\' . __CLASS__)
						->hasParent(__CLASS__)
						->hasInterface('mageekguy\atoum\mock\aggregator')
				->if($generator = new mock\generator())
				->and($generator->shunt('__construct'))
				->then
					->boolean($generator->isShunted('__construct'))->isTrue()
					->object($generator->generate('reflectionMethod'))->isIdenticalTo($generator)
					->boolean($generator->isShunted('__construct'))->isFalse()
		;
	}
}

?>
