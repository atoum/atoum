<?php

namespace mageekguy\atoum\tests\units\test\phpunit\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test\adapter\call\decorators,
	mageekguy\atoum\test\phpunit\mock\generator as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class generator extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->isSubClassOf('\\mageekguy\\atoum\\test\\mock\\generator')
		;
	}

	public function testGetMockedClassCodeForUnknownClass()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = false)
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($unknownClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $unknownClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke($methodName, $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
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
			->and($dummyController = new atoum\mock\controller())
			->and($dummyController->notControlNextNewMock())
			->and($calls = new \mock\mageekguy\atoum\test\adapter\calls())
			->and($dummyController->setCalls($calls))
			->and($dummyController->control($dummy))
			->then
				->when(function() use ($dummy) { $dummy->bar(); })
					->mock($calls)->call('addCall')->withArguments(new atoum\test\adapter\call('bar', array(), new decorators\addClass($dummy)))->once()
				->when(function() use ($dummy) { $dummy->bar(); })
					->mock($calls)->call('addCall')->withArguments(new atoum\test\adapter\call('bar', array(), new decorators\addClass($dummy)))->twice()
		;
	}

	public function testGetMockedClassCodeForRealClass()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->isAbstract = false)
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
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $realClass = uniqid())
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->isAbstract = false)
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
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'' . $realClass . '\', $arguments);' . PHP_EOL .
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

	/** @php < 7.0 */
	public function testGetMockedClassCodeForRealClassWithCallsToParentClassShunted()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($otherReflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->isAbstract = false)
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
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $otherMethod . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
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

	/** @php >= 7.0 */
	public function testGetMockedClassCodeForRealClassWithCallsToParentClassShuntedPhp7()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethodController->hasReturnType = false)
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($otherReflectionMethodController = new mock\controller())
			->and($otherReflectionMethodController->__construct = function() {})
			->and($otherReflectionMethodController->getName = $otherMethod = uniqid())
			->and($otherReflectionMethodController->isConstructor = false)
			->and($otherReflectionMethodController->getParameters = array())
			->and($otherReflectionMethodController->isPublic = true)
			->and($otherReflectionMethodController->isProtected = false)
			->and($otherReflectionMethodController->isPrivate = false)
			->and($otherReflectionMethodController->isFinal = false)
			->and($otherReflectionMethodController->isStatic = false)
			->and($otherReflectionMethodController->isAbstract = false)
			->and($otherReflectionMethodController->returnsReference = false)
			->and($otherReflectionMethodController->hasReturnType = false)
			->and($otherReflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->isAbstract = false)
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
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $otherMethod . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
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
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->isAbstract = false)
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
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($realClass = uniqid())
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = function() { return '__construct'; })
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($realClass = uniqid())
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = function() { return '__construct'; })
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->and($generator->shunt('__construct'))
			->then
				->string($generator->getMockedClassCode($realClass))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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

	/** @php < 7.0 */
	public function testGetMockedClassCodeWithAllIsInterface()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($realClass = uniqid())
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = 'foo')
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use ($realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = null)
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->and($generator->shunt('__construct'))
			->and($generator->allIsInterface())
			->then
				->string($generator->getMockedClassCode($realClass))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function foo()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->foo) === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->foo = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$return = $this->getMockController()->invoke(\'foo\', $arguments);' . PHP_EOL .
					"\t\t" . 'return $return;' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', 'foo'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)

			->if($generator->testedClassIs($realClass))
			->then
				->string($generator->getMockedClassCode($realClass))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function foo()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->foo) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'foo\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'foo\', $arguments);' . PHP_EOL .
					"\t\t\t" . '$return = call_user_func_array(\'parent::foo\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', 'foo'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/** @php >= 7.0 */
	public function testGetMockedClassCodeWithAllIsInterfacePhp7()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($realClass = uniqid())
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = 'foo')
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethodController->hasReturnType = false)
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use ($realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = null)
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->and($generator->shunt('__construct'))
			->and($generator->allIsInterface())
			->then
				->string($generator->getMockedClassCode($realClass))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function foo()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->foo) === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->foo = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$return = $this->getMockController()->invoke(\'foo\', $arguments);' . PHP_EOL .
					"\t\t" . 'return $return;' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', 'foo'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)

			->if($generator->testedClassIs($realClass))
			->then
				->string($generator->getMockedClassCode($realClass))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function foo()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->foo) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'foo\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'foo\', $arguments);' . PHP_EOL .
					"\t\t\t" . '$return = call_user_func_array(\'parent::foo\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', 'foo'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/** @php < 5.6 */
	public function testGetMockedClassCodeWithCloneMethod()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($realClass = uniqid())
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = 'clone')
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->getConstructor = null)
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
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

	public function testGetMockedClassCodeWithShuntedDeprecatedConstructor()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $realClass = uniqid())
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->and($generator->shunt($realClass))
			->then
				->string($generator->getMockedClassCode($realClass))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->' . $realClass . ' = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'' . $realClass . '\', $arguments);' . PHP_EOL .
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

	/** @php < 7.0 */
	public function testGetMockedClassCodeForInterface()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
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
					'final class ' . $realClass . ' implements \\' . $realClass . ', \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke($methodName, $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', '__call'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
			->if($reflectionClassController->implementsInterface = function($interface) { return ($interface == 'traversable' ? true : false); })
			->and($generator->setReflectionClassFactory(function($class) use ($reflectionClass) { return ($class == 'iteratorAggregate' ? new \reflectionClass('iteratorAggregate') : $reflectionClass); }))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' implements \\iteratorAggregate, \\' . $realClass . ', \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function getIterator()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->getIterator) === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->getIterator = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$return = $this->getMockController()->invoke(\'getIterator\', $arguments);' . PHP_EOL .
					"\t\t" . 'return $return;' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke($methodName, $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', 'getiterator', '__call'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
			->if($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
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
			->and($generator->disallowUndefinedMethodUsage())
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' implements \\' . $realClass . ', \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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

	/** @php >= 7.0 */
	public function testGetMockedClassCodeForInterfacePhp7()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethodController->hasReturnType = false)
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
					'final class ' . $realClass . ' implements \\' . $realClass . ', \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke($methodName, $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', '__call'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
			->if($reflectionClassController->implementsInterface = function($interface) { return ($interface == 'traversable' ? true : false); })
			->and($generator->setReflectionClassFactory(function($class) use ($reflectionClass) { return ($class == 'iteratorAggregate' ? new \reflectionClass('iteratorAggregate') : $reflectionClass); }))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' implements \\iteratorAggregate, \\' . $realClass . ', \mageekguy\atoum\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function getIterator()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->getIterator) === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->getIterator = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$return = $this->getMockController()->invoke(\'getIterator\', $arguments);' . PHP_EOL .
					"\t\t" . 'return $return;' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke($methodName, $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', 'getiterator', '__call'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
			->if($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
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
			->and($generator->disallowUndefinedMethodUsage())
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' implements \\' . $realClass . ', \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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

	/** @php < 5.6 */
	public function testGetMockedClassCodeForInterfaceWithConstructorArguments()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionParameterController = new mock\controller())
			->and($reflectionParameterController->__construct = function() {})
			->and($reflectionParameterController->isArray = true)
			->and($reflectionParameterController->getName = 'param')
			->and($reflectionParameterController->isPassedByReference = false)
			->and($reflectionParameterController->isDefaultValueAvailable = false)
			->and($reflectionParameterController->isOptional = false)
			->and($reflectionParameter = new \mock\reflectionParameter(null, null))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array($reflectionParameter))
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
					'final class ' . $realClass . ' implements \\' . $realClass . ', \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
					"\t" . 'public function __construct(array $param, \mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array($param), array_slice(func_get_args(), 1, -1));' . PHP_EOL .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke($methodName, $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', '__call'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/**
	 * @php >= 5.6
	 * @php < 7.0
	 */
	public function testGetMockedClassCodeForInterfaceWithConstructorArgumentsPhp56()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionParameterController = new mock\controller())
			->and($reflectionParameterController->__construct = function() {})
			->and($reflectionParameterController->isArray = true)
			->and($reflectionParameterController->getName = 'param')
			->and($reflectionParameterController->isPassedByReference = false)
			->and($reflectionParameterController->isDefaultValueAvailable = false)
			->and($reflectionParameterController->isOptional = false)
			->and($reflectionParameterController->isVariadic = false)
			->and($reflectionParameter = new \mock\reflectionParameter(null, null))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array($reflectionParameter))
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
					'final class ' . $realClass . ' implements \\' . $realClass . ', \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
					"\t" . 'public function __construct(array $param, \mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array($param), array_slice(func_get_args(), 1, -1));' . PHP_EOL .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke($methodName, $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', '__call'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeForInterfaceWithStaticMethod()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $methodName = uniqid())
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = true)
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
					'final class ' . $realClass . ' implements \\' . $realClass . ', \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
					"\t" . 'public static function ' . $methodName . '()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
					"\t\t" . 'return call_user_func_array(array(\'parent\', \'' . $methodName . '\'), $arguments);' . PHP_EOL .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke($methodName, $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array($methodName, '__construct', '__call'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
			->if($reflectionClassController->implementsInterface = function($interface) { return ($interface == 'traversable' ? true : false); })
			->and($generator->setReflectionClassFactory(function($class) use ($reflectionClass) { return ($class == 'iteratorAggregate' ? new \reflectionClass('iteratorAggregate') : $reflectionClass); }))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' implements \\iteratorAggregate, \\' . $realClass . ', \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
					"\t" . 'public static function ' . $methodName . '()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
					"\t\t" . 'return call_user_func_array(array(\'parent\', \'' . $methodName . '\'), $arguments);' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function getIterator()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->getIterator) === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->getIterator = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$return = $this->getMockController()->invoke(\'getIterator\', $arguments);' . PHP_EOL .
					"\t\t" . 'return $return;' . PHP_EOL .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke($methodName, $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array($methodName, 'getiterator', '__construct', '__call'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/** @php >= 7.0 */
	public function testGetMockedClassCodeForInterfaceWithTypeHint()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionParameterController = new mock\controller())
			->and($reflectionParameterController->__construct = function() {})
			->and($reflectionParameterController->isArray = false)
			->and($reflectionParameterController->isCallable = false)
			->and($reflectionParameterController->getName = 'typeHint')
			->and($reflectionParameterController->isPassedByReference = false)
			->and($reflectionParameterController->isDefaultValueAvailable = false)
			->and($reflectionParameterController->isOptional = false)
			->and($reflectionParameterController->isVariadic = false)
			->and($reflectionParameterController->getClass = null)
			->and($reflectionParameterController->hasType = true)
			->and($reflectionParameterController->getType = 'string')
			->and($reflectionParameter = new \mock\reflectionParameter(null, null))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $methodName = uniqid())
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->getParameters = array($reflectionParameter))
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethodController->hasReturnType = false)
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = null)
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function ' . $methodName . '(string $typeHint)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array($typeHint), array_slice(func_get_args(), 1));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . '$return = call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', $methodName), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/** @php >= 7.0 */
	public function testGetMockedClassCodeForInterfaceWithReturnType()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $methodName = uniqid())
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethodController->hasReturnType = true)
			->and($reflectionMethodController->getReturnType = 'string')
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = null)
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function ' . $methodName . '(): string' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . '$return = call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', $methodName), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/** @php < 7.0 */
	public function testGetMockedClassCodeForRealClassWithoutConstructor()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $methodName = uniqid())
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function ' . $methodName . '()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . '$return = call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', $methodName), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/** @php >= 7.0 */
	public function testGetMockedClassCodeForRealClassWithoutConstructorPhp7()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $methodName = uniqid())
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethodController->hasReturnType = false)
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = null)
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function ' . $methodName . '()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . '$return = call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', $methodName), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGetMockedClassCodeForAbstractClassWithConstructorInInterface()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($publicMethodController = new mock\controller())
			->and($publicMethodController->__construct = function() {})
			->and($publicMethodController->getName = '__construct')
			->and($publicMethodController->isConstructor = true)
			->and($publicMethodController->getParameters = array())
			->and($publicMethodController->isPublic = true)
			->and($publicMethodController->isProtected = false)
			->and($publicMethodController->isPrivate = false)
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
					'final class ' . $className . ' extends \\' . $className . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function __call($methodName, $arguments)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->{$methodName}) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke($methodName, $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall($methodName, $arguments);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', '__call'), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	public function testGenerate()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
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
			->and($generator = new testedClass($test))
			->then
				->object($generator->generate(__CLASS__))->isIdenticalTo($generator)
				->class('\mock\\' . __CLASS__)
					->hasParent(__CLASS__)
					->hasInterface('mageekguy\atoum\mock\aggregator')
			->if($generator = new testedClass($test))
			->and($generator->shunt('__construct'))
			->then
				->boolean($generator->isShunted('__construct'))->isTrue()
				->object($generator->generate('reflectionMethod'))->isIdenticalTo($generator)
				->boolean($generator->isShunted('__construct'))->isFalse()
			->if($generator = new testedClass($test))
			->and($generator->shuntParentClassCalls())
			->then
				->object($generator->generate('reflectionParameter'))->isIdenticalTo($generator)
				->boolean($generator->callsToParentClassAreShunted())->isFalse()
		;
	}

	public function testMethodIsMockable()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($this->mockGenerator->orphanize('__construct'))
			->and($method = new \mock\reflectionMethod($this, $methodName = uniqid()))
			->and($this->calling($method)->getName = $methodName)
			->and($this->calling($method)->isFinal = false)
			->and($this->calling($method)->isStatic = false)
			->and($this->calling($method)->isAbstract = false)
			->and($this->calling($method)->isPrivate = false)
			->and($this->calling($method)->isProtected = false)
			->then
				->boolean($generator->methodIsMockable($method))->isTrue()
			->if($this->calling($method)->isFinal = true)
			->then
				->boolean($generator->methodIsMockable($method))->isFalse()
			->if($this->calling($method)->isFinal = false)
			->and($this->calling($method)->isStatic = true)
			->then
				->boolean($generator->methodIsMockable($method))->isFalse()
			->if($this->calling($method)->isStatic = false)
			->and($this->calling($method)->isPrivate = true)
			->then
				->boolean($generator->methodIsMockable($method))->isFalse()
			->if($this->calling($method)->isPrivate = false)
			->and($this->calling($method)->isProtected = true)
			->then
				->boolean($generator->methodIsMockable($method))->isFalse()
			->if($generator->overload(new mock\php\method($methodName)))
			->then
				->boolean($generator->methodIsMockable($method))->isTrue()
		;
	}

	/** @php < 7.0 */
	public function testMethodIsMockableWithReservedWord($reservedWord)
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($this->mockGenerator->orphanize('__construct'))
			->and($method = new \mock\reflectionMethod($this, $reservedWord))
			->and($this->calling($method)->getName = $reservedWord)
			->and($this->calling($method)->isFinal = false)
			->and($this->calling($method)->isStatic = false)
			->and($this->calling($method)->isAbstract = false)
			->and($this->calling($method)->isPrivate = false)
			->and($this->calling($method)->isProtected = false)
			->then
				->boolean($generator->methodIsMockable($method))->isFalse()
		;
	}

	/** @php >= 7.0 */
	public function testMethodIsMockableWithReservedWordPhp7($reservedWord)
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($this->mockGenerator->orphanize('__construct'))
			->and($method = new \mock\reflectionMethod($this, $reservedWord))
			->and($this->calling($method)->getName = $reservedWord)
			->and($this->calling($method)->isFinal = false)
			->and($this->calling($method)->isStatic = false)
			->and($this->calling($method)->isAbstract = false)
			->and($this->calling($method)->isPrivate = false)
			->and($this->calling($method)->isProtected = false)
			->then
				->boolean($generator->methodIsMockable($method))->isFalse()
		;
	}

	/**
	 * @php >= 5.4
	 * @php < 5.6
	 */
	public function testGetMockedClassCodeWithOrphanizedMethod()
	{
		$this
			->if->mockGenerator->orphanize('__construct')
			->and($a = new \mock\reflectionParameter())
			->and($this->calling($a)->getName = 'a')
			->and($this->calling($a)->isArray = false)
			->and($this->calling($a)->isCallable = false)
			->and($this->calling($a)->getClass = null)
			->and($this->calling($a)->isPassedByReference = false)
			->and($this->calling($a)->isDefaultValueAvailable = false)
			->and($this->calling($a)->isOptional = false)
			->and($b = new \mock\reflectionParameter())
			->and($this->calling($b)->getName = 'b')
			->and($this->calling($b)->isArray = false)
			->and($this->calling($b)->isCallable = false)
			->and($this->calling($b)->getClass = null)
			->and($this->calling($b)->isPassedByReference = false)
			->and($this->calling($b)->isDefaultValueAvailable = false)
			->and($this->calling($b)->isOptional = false)
			->and($c = new \mock\reflectionParameter())
			->and($this->calling($c)->getName = 'c')
			->and($this->calling($c)->isArray = false)
			->and($this->calling($c)->isCallable = false)
			->and($this->calling($c)->getClass = null)
			->and($this->calling($c)->isPassedByReference = false)
			->and($this->calling($c)->isDefaultValueAvailable = false)
			->and($this->calling($c)->isOptional = false)
			->and->mockGenerator->orphanize('__construct')
			->and($constructor = new \mock\reflectionMethod())
			->and($this->calling($constructor)->getName = '__construct')
			->and($this->calling($constructor)->isConstructor = true)
			->and($this->calling($constructor)->getParameters = array($a, $b, $c))
			->and($this->calling($constructor)->isPublic = true)
			->and($this->calling($constructor)->isProtected = false)
			->and($this->calling($constructor)->isPrivate = false)
			->and($this->calling($constructor)->isFinal = false)
			->and($this->calling($constructor)->isStatic = false)
			->and($this->calling($constructor)->isAbstract = false)
			->and($this->calling($constructor)->returnsReference = false)
			->and->mockGenerator->orphanize('__construct')
			->and($class = new \mock\reflectionClass())
			->and($this->calling($class)->getName = $className = uniqid())
			->and($this->calling($class)->isFinal = false)
			->and($this->calling($class)->isInterface = false)
			->and($this->calling($class)->isAbstract = false)
			->and($this->calling($class)->getMethods = array($constructor))
			->and($this->calling($class)->getConstructor = $constructor)
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($className) { return ($class == '\\' . $className); })
			->and($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($generator->setReflectionClassFactory(function() use ($class) { return $class; }))
			->and($generator->setAdapter($adapter))
			->and($generator->orphanize('__construct'))
			->then
				->string($generator->getMockedClassCode($className))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $className . ' extends \\' . $className . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
					"\t" . 'public function __construct($a = null, $b = null, $c = null, \mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array($a, $b, $c), array_slice(func_get_args(), 3, -1));' . PHP_EOL .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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

	/**
	 * @php >= 5.6
	 * @php < 7.0
	 */
	public function testGetMockedClassCodeWithOrphanizedMethodPhp56()
	{
		$this
			->if->mockGenerator->orphanize('__construct')
			->and($a = new \mock\reflectionParameter())
			->and($this->calling($a)->getName = 'a')
			->and($this->calling($a)->isArray = false)
			->and($this->calling($a)->isCallable = false)
			->and($this->calling($a)->getClass = null)
			->and($this->calling($a)->isPassedByReference = false)
			->and($this->calling($a)->isDefaultValueAvailable = false)
			->and($this->calling($a)->isOptional = false)
			->and($this->calling($a)->isVariadic = false)
			->and($b = new \mock\reflectionParameter())
			->and($this->calling($b)->getName = 'b')
			->and($this->calling($b)->isArray = false)
			->and($this->calling($b)->isCallable = false)
			->and($this->calling($b)->getClass = null)
			->and($this->calling($b)->isPassedByReference = false)
			->and($this->calling($b)->isDefaultValueAvailable = false)
			->and($this->calling($b)->isOptional = false)
			->and($this->calling($b)->isVariadic = false)
			->and($c = new \mock\reflectionParameter())
			->and($this->calling($c)->getName = 'c')
			->and($this->calling($c)->isArray = false)
			->and($this->calling($c)->isCallable = false)
			->and($this->calling($c)->getClass = null)
			->and($this->calling($c)->isPassedByReference = false)
			->and($this->calling($c)->isDefaultValueAvailable = false)
			->and($this->calling($c)->isOptional = false)
			->and($this->calling($c)->isVariadic = false)
			->and->mockGenerator->orphanize('__construct')
			->and($constructor = new \mock\reflectionMethod())
			->and($this->calling($constructor)->getName = '__construct')
			->and($this->calling($constructor)->isConstructor = true)
			->and($this->calling($constructor)->getParameters = array($a, $b, $c))
			->and($this->calling($constructor)->isPublic = true)
			->and($this->calling($constructor)->isProtected = false)
			->and($this->calling($constructor)->isPrivate = false)
			->and($this->calling($constructor)->isFinal = false)
			->and($this->calling($constructor)->isStatic = false)
			->and($this->calling($constructor)->isAbstract = false)
			->and($this->calling($constructor)->returnsReference = false)
			->and->mockGenerator->orphanize('__construct')
			->and($class = new \mock\reflectionClass())
			->and($this->calling($class)->getName = $className = uniqid())
			->and($this->calling($class)->isFinal = false)
			->and($this->calling($class)->isInterface = false)
			->and($this->calling($class)->isAbstract = false)
			->and($this->calling($class)->getMethods = array($constructor))
			->and($this->calling($class)->getConstructor = $constructor)
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($className) { return ($class == '\\' . $className); })
			->and($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($generator->setReflectionClassFactory(function() use ($class) { return $class; }))
			->and($generator->setAdapter($adapter))
			->and($generator->orphanize('__construct'))
			->then
				->string($generator->getMockedClassCode($className))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $className . ' extends \\' . $className . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
					"\t" . 'public function __construct($a = null, $b = null, $c = null, \mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array($a, $b, $c), array_slice(func_get_args(), 3, -1));' . PHP_EOL .
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
					"\t\t\t" . '$this->getMockController()->__construct = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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

	/**
	 * @php 5.4
	 * @php < 5.6
	 */
	public function testGetMockedClassCodeWithProtectedAbstractMethod()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($parameterController1 = new mock\controller())
			->and($parameterController1->__construct = function() {})
			->and($parameterController1->isArray = false)
			->and($parameterController1->isCallable = false)
			->and($parameterController1->getClass = null)
			->and($parameterController1->getName = 'arg1')
			->and($parameterController1->isPassedByReference = false)
			->and($parameterController1->isDefaultValueAvailable = false)
			->and($parameterController1->isOptional = false)
			->and($parameter1 = new \mock\reflectionParameter(null, null))
			->and($parameterController2 = new mock\controller())
			->and($parameterController2->__construct = function() {})
			->and($parameterController2->isArray = true)
			->and($parameterController2->isCallable = false)
			->and($parameterController2->getClass = null)
			->and($parameterController2->getName = 'arg2')
			->and($parameterController2->isPassedByReference = true)
			->and($parameterController2->isDefaultValueAvailable = false)
			->and($parameterController2->isOptional = false)
			->and($parameter2 = new \mock\reflectionParameter(null, null))
			->and($publicMethodController = new mock\controller())
			->and($publicMethodController->__construct = function() {})
			->and($publicMethodController->getName = $publicMethodName = uniqid())
			->and($publicMethodController->isConstructor = false)
			->and($publicMethodController->getParameters = array($parameter1, $parameter2))
			->and($publicMethodController->isPublic = true)
			->and($publicMethodController->isProtected = false)
			->and($publicMethodController->isPrivate = false)
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
			->and($protectedMethodController->isPrivate = false)
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
			->and($classController->isAbstract = false)
			->and($class = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($class) { return $class; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($className) { return ($class == '\\' . $className); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($className))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $className . ' extends \\' . $className . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function ' . $publicMethodName . '($arg1, array & $arg2)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array($arg1, & $arg2), array_slice(func_get_args(), 2));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $publicMethodName . ') === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->' . $publicMethodName . ' = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$return = $this->getMockController()->invoke(\'' . $publicMethodName . '\', $arguments);' . PHP_EOL .
					"\t\t" . 'return $return;' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'protected function ' . $protectedMethodName . '()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $protectedMethodName . ') === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->' . $protectedMethodName . ' = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$return = $this->getMockController()->invoke(\'' . $protectedMethodName . '\', $arguments);' . PHP_EOL .
					"\t\t" . 'return $return;' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', $publicMethodName, $protectedMethodName), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/**
	 * @php >= 5.6
	 * @php < 7.0
	 */
	public function testGetMockedClassCodeWithProtectedAbstractMethodPhp56()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($parameterController1 = new mock\controller())
			->and($parameterController1->__construct = function() {})
			->and($parameterController1->isArray = false)
			->and($parameterController1->isCallable = false)
			->and($parameterController1->getClass = null)
			->and($parameterController1->getName = 'arg1')
			->and($parameterController1->isPassedByReference = false)
			->and($parameterController1->isDefaultValueAvailable = false)
			->and($parameterController1->isOptional = false)
			->and($parameterController1->isVariadic = false)
			->and($parameter1 = new \mock\reflectionParameter(null, null))
			->and($parameterController2 = new mock\controller())
			->and($parameterController2->__construct = function() {})
			->and($parameterController2->isArray = true)
			->and($parameterController2->isCallable = false)
			->and($parameterController2->getClass = null)
			->and($parameterController2->getName = 'arg2')
			->and($parameterController2->isPassedByReference = true)
			->and($parameterController2->isDefaultValueAvailable = false)
			->and($parameterController2->isOptional = false)
			->and($parameterController2->isVariadic = false)
			->and($parameter2 = new \mock\reflectionParameter(null, null))
			->and($publicMethodController = new mock\controller())
			->and($publicMethodController->__construct = function() {})
			->and($publicMethodController->getName = $publicMethodName = uniqid())
			->and($publicMethodController->isConstructor = false)
			->and($publicMethodController->getParameters = array($parameter1, $parameter2))
			->and($publicMethodController->isPublic = true)
			->and($publicMethodController->isProtected = false)
			->and($publicMethodController->isPrivate = false)
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
			->and($protectedMethodController->isPrivate = false)
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
			->and($classController->isAbstract = false)
			->and($class = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($class) { return $class; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use ($className) { return ($class == '\\' . $className); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($className))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $className . ' extends \\' . $className . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function ' . $publicMethodName . '($arg1, array & $arg2)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array($arg1, & $arg2), array_slice(func_get_args(), 2));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $publicMethodName . ') === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->' . $publicMethodName . ' = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$return = $this->getMockController()->invoke(\'' . $publicMethodName . '\', $arguments);' . PHP_EOL .
					"\t\t" . 'return $return;' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'protected function ' . $protectedMethodName . '()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $protectedMethodName . ') === false)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->' . $protectedMethodName . ' = function() {};' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . '$return = $this->getMockController()->invoke(\'' . $protectedMethodName . '\', $arguments);' . PHP_EOL .
					"\t\t" . 'return $return;' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', $publicMethodName, $protectedMethodName), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/**
	 * @php 5.4
	 * @php < 5.6
	 */
	public function testGetMockedClassCodeForClassWithCallableTypeHint()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionParameterController = new mock\controller())
			->and($reflectionParameterController->__construct = function() {})
			->and($reflectionParameterController->isArray = false)
			->and($reflectionParameterController->isCallable = true)
			->and($reflectionParameterController->getName = 'callback')
			->and($reflectionParameterController->isPassedByReference = false)
			->and($reflectionParameterController->isDefaultValueAvailable = false)
			->and($reflectionParameterController->isOptional = false)
			->and($reflectionParameter = new \mock\reflectionParameter(null, null))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array($reflectionParameter))
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
			->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
				'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
					"\t" . 'public function __construct(callable $callback, \mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array($callback), array_slice(func_get_args(), 1, -1));' . PHP_EOL .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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

	/** @php 5.6 */
	public function testGetMockedClassCodeForClassWithCallableTypeHintPhp56()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionParameterController = new mock\controller())
			->and($reflectionParameterController->__construct = function() {})
			->and($reflectionParameterController->isArray = false)
			->and($reflectionParameterController->isCallable = true)
			->and($reflectionParameterController->getName = 'callback')
			->and($reflectionParameterController->isPassedByReference = false)
			->and($reflectionParameterController->isDefaultValueAvailable = false)
			->and($reflectionParameterController->isOptional = false)
			->and($reflectionParameterController->isVariadic = false)
			->and($reflectionParameter = new \mock\reflectionParameter(null, null))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array($reflectionParameter))
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
					"\t" . 'public function __construct(callable $callback, \mageekguy\atoum\mock\controller $mockController = null)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array($callback), array_slice(func_get_args(), 1, -1));' . PHP_EOL .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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

	/**
	 * @php >= 5.6
	 * @php < 7.0
	 */
	public function testGetMockedClassCodeForClassWithVariadicArgumentsInConstruct()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionParameterController = new mock\controller())
			->and($reflectionParameterController->__construct = function() {})
			->and($reflectionParameterController->isArray = false)
			->and($reflectionParameterController->isCallable = false)
			->and($reflectionParameterController->getName = 'variadic')
			->and($reflectionParameterController->isPassedByReference = false)
			->and($reflectionParameterController->isDefaultValueAvailable = false)
			->and($reflectionParameterController->isOptional = false)
			->and($reflectionParameterController->isVariadic = true)
			->and($reflectionParameterController->getClass = null)
			->and($reflectionParameter = new \mock\reflectionParameter(null, null))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = '__construct')
			->and($reflectionMethodController->isConstructor = true)
			->and($reflectionMethodController->getParameters = array($reflectionParameter))
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
					"\t" . 'public function __construct(... $variadic)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = func_get_args();' . PHP_EOL .
					"\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
					"\t\t" . 'if ($mockController !== null)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->__construct) === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
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

	/**
	 * @php >= 5.6
	 * @php < 7.0
	 */
	public function testGetMockedClassCodeForClassWithOnlyVariadicArgumentsInMethod()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionParameterController = new mock\controller())
			->and($reflectionParameterController->__construct = function() {})
			->and($reflectionParameterController->isArray = false)
			->and($reflectionParameterController->isCallable = false)
			->and($reflectionParameterController->getName = 'variadic')
			->and($reflectionParameterController->isPassedByReference = false)
			->and($reflectionParameterController->isDefaultValueAvailable = false)
			->and($reflectionParameterController->isOptional = false)
			->and($reflectionParameterController->isVariadic = true)
			->and($reflectionParameterController->getClass = null)
			->and($reflectionParameter = new \mock\reflectionParameter(null, null))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $methodName = uniqid())
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->getParameters = array($reflectionParameter))
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
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
			->and($reflectionClassController->getConstructor = null)
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function ' . $methodName . '(... $variadic)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = func_get_args();' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . '$return = call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', $methodName), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/** @php >= 7.0 */
	public function testGetMockedClassCodeForMethodWithTypeHint()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionParameterController = new mock\controller())
			->and($reflectionParameterController->__construct = function() {})
			->and($reflectionParameterController->isArray = false)
			->and($reflectionParameterController->isCallable = false)
			->and($reflectionParameterController->getName = 'typeHint')
			->and($reflectionParameterController->isPassedByReference = false)
			->and($reflectionParameterController->isDefaultValueAvailable = false)
			->and($reflectionParameterController->isOptional = false)
			->and($reflectionParameterController->isVariadic = false)
			->and($reflectionParameterController->getClass = null)
			->and($reflectionParameterController->hasType = true)
			->and($reflectionParameterController->getType = 'string')
			->and($reflectionParameter = new \mock\reflectionParameter(null, null))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $methodName = uniqid())
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->getParameters = array($reflectionParameter))
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethodController->hasReturnType = false)
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = null)
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function ' . $methodName . '(string $typeHint)' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array($typeHint), array_slice(func_get_args(), 1));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . '$return = call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', $methodName), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/** @php >= 7.0 */
	public function testGetMockedClassCodeForMethodWithReturnType()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $methodName = uniqid())
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethodController->hasReturnType = true)
			->and($reflectionMethodController->getReturnType = 'string')
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = null)
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function ' . $methodName . '(): string' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . '$return = call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', $methodName), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	/** @php >= 7.0 */
	public function testGetMockedClassCodeForMethodWithReservedWord()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($generator = new testedClass($test))
			->and($reflectionMethodController = new mock\controller())
			->and($reflectionMethodController->__construct = function() {})
			->and($reflectionMethodController->getName = $methodName = 'list')
			->and($reflectionMethodController->isConstructor = false)
			->and($reflectionMethodController->getParameters = array())
			->and($reflectionMethodController->isPublic = true)
			->and($reflectionMethodController->isProtected = false)
			->and($reflectionMethodController->isPrivate = false)
			->and($reflectionMethodController->isFinal = false)
			->and($reflectionMethodController->isStatic = false)
			->and($reflectionMethodController->isAbstract = false)
			->and($reflectionMethodController->returnsReference = false)
			->and($reflectionMethodController->hasReturnType = false)
			->and($reflectionMethod = new \mock\reflectionMethod(null, null))
			->and($reflectionClassController = new mock\controller())
			->and($reflectionClassController->__construct = function() {})
			->and($reflectionClassController->getName = function() use (& $realClass) { return $realClass; })
			->and($reflectionClassController->isFinal = false)
			->and($reflectionClassController->isInterface = false)
			->and($reflectionClassController->getMethods = array($reflectionMethod))
			->and($reflectionClassController->getConstructor = null)
			->and($reflectionClassController->isAbstract = false)
			->and($reflectionClass = new \mock\reflectionClass(null))
			->and($generator->setReflectionClassFactory(function() use ($reflectionClass) { return $reflectionClass; }))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = function($class) use (& $realClass) { return ($class == '\\' . $realClass); })
			->and($generator->setAdapter($adapter))
			->then
				->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
					'namespace mock {' . PHP_EOL .
					'final class ' . $realClass . ' extends \\' . $realClass . ' implements \mageekguy\atoum\test\phpunit\mock\aggregator' . PHP_EOL .
					'{' . PHP_EOL .
					$this->getMockControllerMethods() .
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
					"\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public function ' . $methodName . '()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
					"\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t\t" . 'else' . PHP_EOL .
					"\t\t" . '{' . PHP_EOL .
					"\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . '$return = call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL .
					"\t\t\t" . 'return $return;' . PHP_EOL .
					"\t\t" . '}' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					"\t" . 'public static function getMockedMethods()' . PHP_EOL .
					"\t" . '{' . PHP_EOL .
					"\t\t" . 'return ' . var_export(array('__construct', $methodName), true) . ';' . PHP_EOL .
					"\t" . '}' . PHP_EOL .
					'}' . PHP_EOL .
					'}'
				)
		;
	}

	protected function getMockControllerMethods()
	{
		return
			"\t" . 'public function getMockController()' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . '$mockController = \mageekguy\atoum\mock\controller::getForMock($this);' . PHP_EOL .
			"\t\t" . 'if ($mockController === null)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->setMockController($mockController = new \mageekguy\atoum\mock\controller());' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'return $mockController;' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			"\t" . 'public function setMockController(\mageekguy\atoum\mock\controller $controller)' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'return $controller->control($this);' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			"\t" . 'public function resetMockController()' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . '\mageekguy\atoum\mock\controller::getForMock($this)->reset();' . PHP_EOL .
			"\t\t" . 'return $this;' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			"\t" . 'protected $phpUnitMockdefinition;' . PHP_EOL .
			"\t" . 'public function getMockDefinition()' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'if (null === $this->phpUnitMockdefinition)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->phpUnitMockdefinition = new \mageekguy\atoum\test\phpunit\mock\definition($this);' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'return $this->phpUnitMockdefinition;' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			"\t" . 'public function expects($expectation)' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'return $this->getMockDefinition()->expects($expectation);' . PHP_EOL .
			"\t" . '}' . PHP_EOL
		;
	}

	protected function testMethodIsMockableWithReservedWordDataProvider()
	{
		# See http://www.php.net/manual/en/reserved.keywords.php
		return array(
			'__halt_compiler',
			'abstract',
			'and',
			'array',
			'as',
			'break',
			'callable',
			'case',
			'catch',
			'class',
			'clone',
			'const',
			'continue',
			'declare',
			'default',
			'die',
			'do',
			'echo',
			'else',
			'elseif',
			'empty',
			'enddeclare',
			'endfor',
			'endforeach',
			'endif',
			'endswitch',
			'endwhile',
			'eval',
			'exit',
			'extends',
			'final',
			'for',
			'foreach',
			'function',
			'global',
			'goto',
			'if',
			'implements',
			'include',
			'include_once',
			'instanceof',
			'insteadof',
			'interface',
			'isset',
			'list',
			'namespace',
			'new',
			'or',
			'print',
			'private',
			'protected',
			'public',
			'require',
			'require_once',
			'return',
			'static',
			'switch',
			'throw',
			'trait',
			'try',
			'unset',
			'use',
			'var',
			'while',
			'xor'
		);
	}

	protected function testMethodIsMockableWithReservedWordPHP7DataProvider()
	{
		# See http://www.php.net/manual/en/reserved.keywords.php
		return array(
			'__halt_compiler',
		);
	}
}
