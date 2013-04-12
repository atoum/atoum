<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\mock\generator as testedClass
;

require_once __DIR__ . '/../../runner.php';

class generator extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->getAdapter())->isEqualTo(new atoum\adapter())
				->boolean($generator->callsToParentClassAreShunted())->isFalse()
				->object($defaultPhpMethodFactory = $generator->getPhpMethodFactory())->isInstanceOf('\closure')
				->object($defaultPhpMethodFactory($method = uniqid()))->isEqualTo(new mock\php\method($method))
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($generator)
				->object($generator->getAdapter())->isIdenticalTo($adapter)
				->object($generator->setAdapter())->isIdenticalTo($generator)
				->object($generator->getAdapter())
					->isInstanceOf('mageekguy\atoum\adapter')
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testSetPhpMethodFactory()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setPhpMethodFactory($factory = function() {}))->isIdenticalTo($generator)
				->object($generator->getPhpMethodFactory())->isIdenticalTo($factory)
				->object($generator->setPhpMethodFactory())->isIdenticalTo($generator)
				->object($defaultPhpMethodFactory = $generator->getPhpMethodFactory())
					->isInstanceOf('closure')
					->isNotIdenticalTo($factory)
				->object($defaultPhpMethodFactory($method = uniqid()))->isEqualTo(new mock\php\method($method))
		;
	}

	public function testSetReflectionClassFactory()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setReflectionClassFactory($factory = function() {}))->isIdenticalTo($generator)
				->object($generator->getReflectionClassFactory())->isIdenticalTo($factory)
				->object($generator->setReflectionClassFactory())->isIdenticalTo($generator)
				->object($defaultReflectionClassFactory = $generator->getReflectionClassFactory())
					->isInstanceOf('closure')
					->isNotIdenticalTo($factory)
				->object($defaultReflectionClassFactory($this))->isEqualTo(new \reflectionClass($this))
		;
	}

	public function testSetDefaultNamespace()
	{
		$this
			->if($generator = new testedClass())
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

	public function testShuntCallsToParentClass()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->shuntParentClassCalls())->isIdenticalTo($generator)
				->boolean($generator->callsToParentClassAreShunted())->isTrue()
		;
	}

	public function testUnshuntParentClassCalls()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->unshuntParentClassCalls())->isIdenticalTo($generator)
				->boolean($generator->callsToParentClassAreShunted())->isFalse()
			->if($generator->shuntParentClassCalls())
			->then
				->object($generator->unshuntParentClassCalls())->isIdenticalTo($generator)
				->boolean($generator->callsToParentClassAreShunted())->isFalse()
		;
	}

	public function testOverload()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->overload(new mock\php\method($method = uniqid())))->isIdenticalTo($generator)
				->boolean($generator->isOverloaded($method))->isTrue()
		;
	}

	public function testIsOverloaded()
	{
		$this
			->if($generator = new testedClass())
			->then
				->boolean($generator->isOverloaded(uniqid()))->isFalse()
			->if($generator->overload(new mock\php\method($method = uniqid())))
			->then
				->boolean($generator->isOverloaded($method))->isTrue()
		;
	}

	public function testGetOverload()
	{
		$this
			->if($generator = new testedClass())
			->then
				->variable($generator->getOverload(uniqid()))->isNull()
			->if($generator->overload($overload = new mock\php\method(uniqid())))
			->then
				->object($generator->getOverload($overload->getName()))->isIdenticalTo($overload)
		;
	}

	public function testShunt()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->shunt($method = uniqid()))->isIdenticalTo($generator)
				->boolean($generator->isShunted($method))->isTrue()
				->boolean($generator->isShunted(strtoupper($method)))->isTrue()
				->boolean($generator->isShunted(strtolower($method)))->isTrue()
				->boolean($generator->isShunted(uniqid()))->isFalse()
		;
	}

	public function testOrphanize()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->orphanize($method = uniqid()))->isIdenticalTo($generator)
				->boolean($generator->isOrphanized($method))->isTrue()
				->boolean($generator->isShunted($method))->isTrue()
		;
	}

	public function testGetMockedClassCodeForUnknownClass()
	{
		$this
			->if($generator = new testedClass())
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = false)
			->and($generator->setAdapter($adapter))
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__call'), true) . ';' . PHP_EOL .
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
			->if($generator = new testedClass())
			->and($reflectionMethodController = new mock\controller())
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
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = $reflectionMethod)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t\t\t" . '$this->getMockController()->addCall(\'__construct\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'call_user_func_array(\'parent::__construct\', $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeForRealClassWithDeprecatedConstructor()
	{
		$this
			->if($generator = new testedClass())
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $realClass = uniqid())
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = $realClass)
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = $reflectionMethod)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t\t" . 'if (isset($this->getMockController()->' . $realClass . ') === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->mockController->invoke(\'' . $realClass . '\', $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else if (isset($this->getMockController()->__construct) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->mockController->invoke(\'__construct\', $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'' . $realClass . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'call_user_func_array(\'parent::' . $realClass . '\', $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array($realClass), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeForRealClassWithCallsToParentClassShunted()
	{
		$this
			->if($generator = new testedClass())
			->and($reflectionMethodController = new mock\controller())
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
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($otherReflectionMethodController = new mock\controller())
			->and($otherReflectionMethodController->__construct = function() {})
			->and($otherReflectionMethodController->getName = $otherMethod = uniqid())
			->and($otherReflectionMethodController->isConstructor = false)
			->and($otherReflectionMethodController->getParameters = array())
			->and($otherReflectionMethodController->isPublic = true)
			->and($otherReflectionMethodController->isProtected = false)
			->and($otherReflectionMethodController->isFinal = false)
			->and($otherReflectionMethodController->isStatic = false)
			->and($otherReflectionMethodController->isAbstract = false)
			->and($otherReflectionMethodController->returnsReference = false)
			->and($otherReflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod, $otherReflectionMethod))
			->and($reflectionClassController->getConstructor = $reflectionMethod)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->and($generator->shuntParentClassCalls())
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t\t\t" . '$this->getMockController()->addCall(\'__construct\', $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function ' . $otherMethod . '()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $otherMethod . ') === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . 'return $this->mockController->invoke(\'' . $otherMethod . '\', $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'' . $otherMethod . '\', $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', $otherMethod), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeWithOverloadMethod()
	{
		$this
			->if($generator = new testedClass())
			->and($reflectionMethodController = new mock\controller())
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
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = $reflectionMethod)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->and($overloadedMethod = new mock\php\method('__construct'))
			->and($overloadedMethod->addArgument($argument = new mock\php\method\argument(uniqid())))
			->and($generator->overload($overloadedMethod))
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t\t\t" . '$this->getMockController()->addCall(\'__construct\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'call_user_func_array(\'parent::__construct\', $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeWithAbstractMethod()
	{
		$this
			->if($generator = new testedClass())
			->and($realClass = uniqid())
			->and($reflectionMethodController = new mock\controller())
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
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use ($realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = $reflectionMethod)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeWithShuntedMethod()
	{
		$this
			->if($generator = new testedClass())
			->and($realClass = uniqid())
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = function() { return '__construct'; })
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use ($realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = $reflectionMethod)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->and($generator->shunt('__construct'))
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeWithShuntedDeprecatedConstructor()
	{
		$this
			->if($generator = new testedClass())
			->and($realClass = uniqid())
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $realClass = uniqid())
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = $realClass)
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = $reflectionMethod)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->and($generator->shunt($realClass))
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t\t" . 'if (isset($this->getMockController()->' . $realClass . ') === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->mockController->' . $realClass . ' = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->mockController->invoke(\'' . $realClass . '\', $arguments);' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array($realClass), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeForInterface()
	{
		$this
			->if($generator = new testedClass())
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = function() { return '__construct'; })
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = true)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->isInstantiable = false)
			->and($reflectionClassController->implementsInterface = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
			;

			$this
			->if($reflectionClassController->implementsInterface = function($interface) { return ($interface == 'traversable' ? true : false); })
			->and($generator->setReflectionClassFactory(function($class) use ($reflectionClass) { return ($class == 'iterator' ? new \reflectionClass('iterator') : $reflectionClass); }))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' implements \\iterator, \\' . $realClass . ', \mageekguy\atoum\mock\aggregator' . PHP_EOL .
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t" . 'public function current()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->current) === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->mockController->current = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'return $this->mockController->invoke(\'current\', func_get_args());' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function next()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->next) === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->mockController->next = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'return $this->mockController->invoke(\'next\', func_get_args());' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function key()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->key) === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->mockController->key = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'return $this->mockController->invoke(\'key\', func_get_args());' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function valid()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->valid) === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->mockController->valid = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'return $this->mockController->invoke(\'valid\', func_get_args());' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function rewind()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->rewind) === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->mockController->rewind = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'return $this->mockController->invoke(\'rewind\', func_get_args());' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', 'current', 'next', 'key', 'valid', 'rewind'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeForRealClassWithoutConstructor()
	{
		$this
			->if($generator = new testedClass())
			->and($reflectionMethodController = new mock\controller())
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
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = null)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array($methodName, '__construct'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeWithProtectedAbstractMethod()
	{
		$this
			->if($generator = new testedClass())
			->and($publicMethodController = new mock\controller())
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
			->and($protectedMethod = new \mock\reflectionMethod(null, null))
			->and($classController = new mock\controller())
			->and($classController->__construct = function() {})
			->and($classController->getName = $className = uniqid())
			->and($classController->isFinal = false)
			->and($classController->isInterface = false)
			->and($classController->getMethods = array($publicMethod, $protectedMethod))
			->and($classController->getConstructor = null)
			->and($class = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($class) { return $class; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($className) { return ($class == '\\' . $className); })
			->and($generator->setAdapter($adapter))
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t" . 'protected function ' . $protectedMethodName . '()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $protectedMethodName . ') === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->mockController->' . $protectedMethodName . ' = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'return $this->mockController->invoke(\'' . $protectedMethodName . '\', $arguments);' . PHP_EOL .
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
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array($publicMethodName, $protectedMethodName, '__construct'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeForAbstractClassWithConstructorInInterface()
	{
		$this
			->if($generator = new testedClass())
			->and($publicMethodController = new mock\controller())
			->and($publicMethodController->__construct = function() {})
			->and($publicMethodController->getName = '__construct')
			->and($publicMethodController->isConstructor = true)
			->and($publicMethodController->getParameters = array())
			->and($publicMethodController->isPublic = true)
			->and($publicMethodController->isProtected = false)
			->and($publicMethodController->isFinal = false)
			->and($publicMethodController->isStatic = false)
			->and($publicMethodController->isAbstract = true)
			->and($publicMethodController->returnsReference = false)
			->and($publicMethod = new \mock\reflectionMethod(null, null))
			->and($classController = new mock\controller())
			->and($classController->__construct = function() {})
			->and($classController->getName = $className = uniqid())
			->and($classController->isFinal = false)
			->and($classController->isInterface = false)
			->and($classController->isAbstract = true)
			->and($classController->getMethods = array($publicMethod))
			->and($classController->getConstructor = $publicMethod)
			->and($class = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($class) { return $class; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($className) { return ($class == '\\' . $className); })
			->and($generator->setAdapter($adapter))
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
					"\t\t\t" . '$this->mockController = $controller;' . PHP_EOL .
					"\t\t\t" . '$controller->control($this);' . PHP_EOL .
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
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGenerate()
	{
		$this
			->if($generator = new testedClass())
			->then
				->exception(function() use ($generator) { $generator->generate(''); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Class name \'\' is invalid')
				->exception(function() use ($generator) { $generator->generate('\\'); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Class name \'\\\' is invalid')
				->exception(function() use ($generator, & $class) { $generator->generate($class = ('\\' . uniqid() . '\\')); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Class name \'' . $class . '\' is invalid')
			->if($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = false)
			->and($adapter->interface_exists = false)
			->and($generator->setAdapter($adapter))
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
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
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
			->and($generator = new testedClass())
			->then
				->object($generator->generate(__CLASS__))->isIdenticalTo($generator)
				->class('\mock\\' . __CLASS__)
					->hasParent(__CLASS__)
					->hasInterface('mageekguy\atoum\mock\aggregator')
			->if($generator = new testedClass())
			->and($generator->shunt('__construct'))
			->then
				->boolean($generator->isShunted('__construct'))->isTrue()
				->object($generator->generate('reflectionMethod'))->isIdenticalTo($generator)
				->boolean($generator->isShunted('__construct'))->isFalse()
		;
	}
}
