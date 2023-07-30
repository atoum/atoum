<?php

namespace atoum\atoum\tests\units\mock;

use atoum\atoum;
use atoum\atoum\mock;
use atoum\atoum\mock\generator as testedClass;
use atoum\atoum\test\adapter\call\decorators;

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
                    ->isInstanceOf(atoum\adapter::class)
                    ->isNotIdenticalTo($adapter)
                    ->isEqualTo(new atoum\adapter())
        ;
    }

    public function testSetParameterAnalyzer()
    {
        $this
            ->if($generator = new testedClass())
            ->then
                ->object($generator->setParameterAnalyzer($analyzer = new atoum\tools\parameter\analyzer()))->isIdenticalTo($generator)
                ->object($generator->getParameterAnalyzer())->isIdenticalTo($analyzer)
                ->object($generator->setParameterAnalyzer())->isIdenticalTo($generator)
                ->object($generator->getParameterAnalyzer())
                    ->isInstanceOf(atoum\tools\parameter\analyzer::class)
                    ->isNotIdenticalTo($analyzer)
                    ->isEqualTo(new atoum\tools\parameter\analyzer())
        ;
    }

    public function testSetReflectionClassFactory()
    {
        $this
            ->if($generator = new testedClass())
            ->then
                ->object($generator->setReflectionClassFactory($factory = function () {
                }))->isIdenticalTo($generator)
                ->object($generator->getReflectionClassFactory())->isIdenticalTo($factory)
                ->object($generator->setReflectionClassFactory())->isIdenticalTo($generator)
                ->object($defaultReflectionClassFactory = $generator->getReflectionClassFactory())
                    ->isInstanceOf(\closure::class)
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
                ->string($generator->getDefaultNamespace())->isEqualTo($namespace)
                ->object($generator->setDefaultNamespace('\\' . $namespace))->isIdenticalTo($generator)
                ->string($generator->getDefaultNamespace())->isEqualTo($namespace)
                ->object($generator->setDefaultNamespace('\\' . $namespace . '\\'))->isIdenticalTo($generator)
                ->string($generator->getDefaultNamespace())->isEqualTo($namespace)
                ->object($generator->setDefaultNamespace($namespace . '\\'))->isIdenticalTo($generator)
                ->string($generator->getDefaultNamespace())->isEqualTo($namespace)
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

    public function testAllIsInterface()
    {
        $this
            ->if($generator = new testedClass())
            ->then
                ->object($generator->allIsInterface())->isIdenticalTo($generator)
        ;
    }

    public function testTestedClassIs()
    {
        $this
            ->if($generator = new testedClass())
            ->then
                ->object($generator->testedClassIs(uniqid()))->isIdenticalTo($generator)
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

    public function testDisallowUndefinedMethodUsage()
    {
        $this
            ->if($generator = new testedClass())
            ->then
                ->object($generator->disallowUndefinedMethodUsage())->isIdenticalTo($generator)
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
                    'final class ' . $unknownClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export(['__call'], true) . ';' . PHP_EOL .
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
            ->and($calls = new \mock\atoum\atoum\test\adapter\calls())
            ->and($dummyController->setCalls($calls))
            ->and($dummyController->control($dummy))
            ->then
                ->when(function () use ($dummy) {
                    $dummy->bar();
                })
                    ->mock($calls)->call('addCall')->withArguments(new atoum\test\adapter\call('bar', [], new decorators\addClass($dummy)))->once()
                ->when(function () use ($dummy) {
                    $dummy->bar();
                })
                    ->mock($calls)->call('addCall')->withArguments(new atoum\test\adapter\call('bar', [], new decorators\addClass($dummy)))->twice()
        ;
    }

    public function testGetMockedClassCodeForRealClass()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = '__construct')
            ->and($reflectionMethodController->isConstructor = true)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = $reflectionMethod)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t\t" . 'call_user_func_array([parent::class, \'__construct\'], $arguments);' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct'], true) . ';' . PHP_EOL .
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
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $realClass = uniqid())
            ->and($reflectionMethodController->isConstructor = true)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = $realClass)
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = $reflectionMethod)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->then
                ->string($generator->getMockedClassCode($realClass))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t\t" . 'call_user_func_array([parent::class, \'' . $realClass . '\'], $arguments);' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export([$realClass], true) . ';' . PHP_EOL .
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
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = '__construct')
            ->and($reflectionMethodController->isConstructor = true)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($otherReflectionMethodController = new mock\controller())
            ->and($otherReflectionMethodController->__construct = function () {
            })
            ->and($otherReflectionMethodController->getName = $otherMethod = uniqid())
            ->and($otherReflectionMethodController->isConstructor = false)
            ->and($otherReflectionMethodController->getParameters = [])
            ->and($otherReflectionMethodController->isPublic = true)
            ->and($otherReflectionMethodController->isProtected = false)
            ->and($otherReflectionMethodController->isPrivate = false)
            ->and($otherReflectionMethodController->isFinal = false)
            ->and($otherReflectionMethodController->isStatic = false)
            ->and($otherReflectionMethodController->isAbstract = false)
            ->and($otherReflectionMethodController->returnsReference = false)
            ->and($otherReflectionMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '>=') ? $otherReflectionMethodController->hasTentativeReturnType = false : true)
            ->and($otherReflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod, $otherReflectionMethod])
            ->and($reflectionClassController->getConstructor = $reflectionMethod)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->and($generator->shuntParentClassCalls())
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export(['__construct', $otherMethod], true) . ';' . PHP_EOL .
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
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = '__construct')
            ->and($reflectionMethodController->isConstructor = true)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = $reflectionMethod)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->and($overloadedMethod = new mock\php\method('__construct'))
            ->and($overloadedMethod->addArgument($argument = new mock\php\method\argument(uniqid())))
            ->and($generator->overload($overloadedMethod))
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . '' . $overloadedMethod . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(' . $argument . '), array_slice(func_get_args(), 1, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t\t" . 'call_user_func_array([parent::class, \'__construct\'], $arguments);' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct'], true) . ';' . PHP_EOL .
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
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = function () {
                return '__construct';
            })
            ->and($reflectionMethodController->isConstructor = true)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = true)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use ($realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = $reflectionMethod)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use ($realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->then
                ->string($generator->getMockedClassCode($realClass))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export(['__construct'], true) . ';' . PHP_EOL .
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
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = function () {
                return '__construct';
            })
            ->and($reflectionMethodController->isConstructor = true)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use ($realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = $reflectionMethod)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use ($realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->and($generator->shunt('__construct'))
            ->then
                ->string($generator->getMockedClassCode($realClass))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export(['__construct'], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
        ;
    }

    public function testGetMockedClassCodeWithAllIsInterface()
    {
        $this
            ->if($generator = new testedClass())
            ->and($realClass = uniqid())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = 'foo')
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use ($realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use ($realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->and($generator->shunt('__construct'))
            ->and($generator->allIsInterface())
            ->then
                ->string($generator->getMockedClassCode($realClass))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t\t" . '$this->getMockController()->foo = function() {' . PHP_EOL .
                    "\t\t\t" . '};' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . '$return = $this->getMockController()->invoke(\'foo\', $arguments);' . PHP_EOL .
                    "\t\t" . 'return $return;' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct', 'foo'], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
            ->if($generator->testedClassIs($realClass))
            ->then
                ->string($generator->getMockedClassCode($realClass))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct', 'foo'], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )

            ->given($generator = new testedClass())
            ->if($generator->allIsInterface())
            ->then
                ->string($generator->getMockedClassCode('atoum\atoum\tests\units\mock\classWithVariadicInConstructor'))->isEqualTo(
                    'namespace mock\atoum\atoum\tests\units\mock {' . PHP_EOL .
                    'final class classWithVariadicInConstructor extends \atoum\atoum\tests\units\mock\classWithVariadicInConstructor implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export(['__construct'], true) . ';' . PHP_EOL .
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
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $realClass = uniqid())
            ->and($reflectionMethodController->isConstructor = true)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = $realClass)
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = $reflectionMethod)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use ($realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->and($generator->shunt($realClass))
            ->then
                ->string($generator->getMockedClassCode($realClass))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export([$realClass], true) . ';' . PHP_EOL .
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
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = '__construct')
            ->and($reflectionMethodController->isConstructor = true)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = true)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->isInstantiable = false)
            ->and($reflectionClassController->implementsInterface = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' implements \\' . $realClass . ', \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . 'if ($mockController !== null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . 'if (isset($this->getMockController()->__construct) === false)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->getMockController()->__construct = function() {' . PHP_EOL .
                    "\t\t\t" . '};' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export(['__construct', '__call'], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
            ->if($reflectionClassController->implementsInterface = function ($interface) {
                return ($interface == 'traversable' ? true : false);
            })
            ->and($generator->setReflectionClassFactory(function ($class) use ($reflectionClass) {
                return ($class == 'iteratorAggregate' ? new \reflectionClass('iteratorAggregate') : $reflectionClass);
            }))
            ->and($getIteratorReturnType = version_compare(phpversion(), '8.1', '>=') ? ': \\Traversable' : '')
            ->and($getIteratorMockedReturn = version_compare(phpversion(), '8.1', '>=') ? "\t\t\t\t" . 'return null;' . PHP_EOL : '')
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' implements \\iteratorAggregate, \\' . $realClass . ', \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . 'if ($mockController !== null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . 'if (isset($this->getMockController()->__construct) === false)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->getMockController()->__construct = function() {' . PHP_EOL .
                    "\t\t\t" . '};' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public function getIterator()' . $getIteratorReturnType . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
                    "\t\t" . 'if (isset($this->getMockController()->getIterator) === false)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->getMockController()->getIterator = function() {' . PHP_EOL .
                    $getIteratorMockedReturn .
                    "\t\t\t" . '};' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export(['__construct', 'getiterator', '__call'], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
            ->if($generator = new testedClass())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = '__construct')
            ->and($reflectionMethodController->isConstructor = true)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = true)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->isInstantiable = false)
            ->and($reflectionClassController->implementsInterface = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->and($generator->disallowUndefinedMethodUsage())
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' implements \\' . $realClass . ', \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . 'if ($mockController !== null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . 'if (isset($this->getMockController()->__construct) === false)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->getMockController()->__construct = function() {' . PHP_EOL .
                    "\t\t\t" . '};' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . '$this->getMockController()->invoke(\'__construct\', $arguments);' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct'], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
        ;
    }

    public function testGetMockedClassCodeForInterfaceWithConstructorArguments()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionParameterController = new mock\controller())
            ->and($reflectionParameterController->__construct = function () {
            })
            ->and($reflectionParameterController->getName = 'param')
            ->and($reflectionParameterController->isPassedByReference = false)
            ->and($reflectionParameterController->isDefaultValueAvailable = false)
            ->and($reflectionParameterController->isOptional = false)
            ->and($reflectionParameterController->isVariadic = false)
            ->and($reflectionParameterController->allowsNull = false)
            ->and($reflectionParameter = new \mock\reflectionParameter([uniqid(), uniqid()], 0))
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = '__construct')
            ->and($reflectionMethodController->isConstructor = true)
            ->and($reflectionMethodController->getParameters = [$reflectionParameter])
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = true)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->isInstantiable = false)
            ->and($reflectionClassController->implementsInterface = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->and($analyzerController = new mock\controller())
            ->and($analyzerController->__construct = function () {
            })
            ->and($analyzerController->getTypeHintString = 'array')
            ->and($analyzer = new \mock\atoum\atoum\tools\parameter\analyzer())
            ->and($generator->setParameterAnalyzer($analyzer))
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' implements \\' . $realClass . ', \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(array $param, \atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array($param), array_slice(func_get_args(), 1, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . 'if ($mockController !== null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . 'if (isset($this->getMockController()->__construct) === false)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->getMockController()->__construct = function() {' . PHP_EOL .
                    "\t\t\t" . '};' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export(['__construct', '__call'], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
        ;
    }

    public function testGetMockedClassCodeForInterfaceWithTypeHint()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionParameterController = new mock\controller())
            ->and($reflectionParameterController->__construct = function () {
            })
            ->and($reflectionParameterController->getName = 'typeHint')
            ->and($reflectionParameterController->isPassedByReference = false)
            ->and($reflectionParameterController->isDefaultValueAvailable = false)
            ->and($reflectionParameterController->isOptional = false)
            ->and($reflectionParameterController->isVariadic = false)
            ->and($reflectionParameterController->allowsNull = false)
            ->and($reflectionParameter = new \mock\reflectionParameter([uniqid(), uniqid()], 0))
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = uniqid())
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [$reflectionParameter])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->and($analyzerController = new mock\controller())
            ->and($analyzerController->__construct = function () {
            })
            ->and($analyzerController->getTypeHintString = 'string')
            ->and($analyzer = new \mock\atoum\atoum\tools\parameter\analyzer())
            ->and($generator->setParameterAnalyzer($analyzer))
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                    "\t\t\t" . 'return $return;' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct', $methodName], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
        ;
    }

    public function testGetMockedClassCodeForInterfaceWithReturnType()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionTypeController = new mock\controller())
            ->and($reflectionTypeController->__construct = function () {
            })
            ->and($reflectionTypeController->isBuiltin = true)
            ->and($reflectionTypeController->allowsNull = false)
            ->and($reflectionTypeController->__toString = $returnType = 'string')
            ->and($reflectionType = new \mock\reflectionType())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = uniqid())
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = true)
            ->and($reflectionMethodController->getReturnType = $reflectionType)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t" . 'public function ' . $methodName . '(): ' . $returnType . PHP_EOL .
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
                    "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                    "\t\t\t" . 'return $return;' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct', $methodName], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
        ;
    }

    public function testGetMockedClassCodeForInterfaceWithReturnTypeNotBuiltIn()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionTypeController = new mock\controller())
            ->and($reflectionTypeController->__construct = function () {
            })
            ->and($reflectionTypeController->isBuiltin = false)
            ->and($reflectionTypeController->allowsNull = false)
            ->and($reflectionTypeController->__toString = $returnType = 'Mock\Foo')
            ->and($reflectionType = new \mock\reflectionType())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = uniqid())
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = true)
            ->and($reflectionMethodController->getReturnType = $reflectionType)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->then
            ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                'namespace mock {' . PHP_EOL .
                'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                '{' . PHP_EOL .
                $this->getMockControllerMethods() .
                "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                "\t\t" . '{' . PHP_EOL .
                "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                "\t" . 'public function ' . $methodName . '(): \\' . $returnType . PHP_EOL .
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
                "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                "\t\t\t" . 'return $return;' . PHP_EOL .
                "\t\t" . '}' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'return ' . var_export(['__construct', $methodName], true) . ';' . PHP_EOL .
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
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = uniqid())
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                    "\t\t\t" . 'return $return;' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct', $methodName], true) . ';' . PHP_EOL .
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
            ->and($publicMethodController->__construct = function () {
            })
            ->and($publicMethodController->getName = '__construct')
            ->and($publicMethodController->isConstructor = true)
            ->and($publicMethodController->getParameters = [])
            ->and($publicMethodController->isPublic = true)
            ->and($publicMethodController->isProtected = false)
            ->and($publicMethodController->isPrivate = false)
            ->and($publicMethodController->isFinal = false)
            ->and($publicMethodController->isStatic = false)
            ->and($publicMethodController->isAbstract = true)
            ->and($publicMethodController->returnsReference = false)
            ->and($publicMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($classController = new mock\controller())
            ->and($classController->__construct = function () {
            })
            ->and($classController->getName = $className = uniqid())
            ->and($classController->isFinal = false)
            ->and($classController->isInterface = false)
            ->and($classController->isAbstract = true)
            ->and($classController->getMethods = [$publicMethod])
            ->and($classController->getConstructor = $publicMethod)
            ->and($class = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($class) {
                return $class;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use ($className) {
                return ($class == '\\' . $className);
            })
            ->and($generator->setAdapter($adapter))
            ->then
                ->string($generator->getMockedClassCode($className))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $className . ' extends \\' . $className . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export(['__construct', '__call'], true) . ';' . PHP_EOL .
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
                ->exception(function () use ($generator) {
                    $generator->generate('');
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Class name \'\' is invalid')
                ->exception(function () use ($generator) {
                    $generator->generate('\\');
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Class name \'\\\' is invalid')
                ->exception(function () use ($generator, & $class) {
                    $generator->generate($class = ('\\' . uniqid() . '\\'));
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
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
                    ->hasInterface(atoum\mock\aggregator::class)
            ->if($class = '\\' . uniqid('unknownClass'))
            ->then
                ->object($generator->generate($class))->isIdenticalTo($generator)
                ->class('\mock' . $class)
                    ->hasNoParent()
                    ->hasInterface(atoum\mock\aggregator::class)
            ->if($adapter->class_exists = true)
            ->and($class = uniqid())
            ->then
                ->exception(function () use ($generator, $class) {
                    $generator->generate($class);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Class \'\mock\\' . $class . '\' already exists')
            ->if($class = '\\' . uniqid())
            ->then
                ->exception(function () use ($generator, $class) {
                    $generator->generate($class);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Class \'\mock' . $class . '\' already exists')
            ->if($class = uniqid())
            ->and($adapter->class_exists = function ($arg) use ($class) {
                return $arg === '\\' . $class;
            })
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->isFinal = true)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid(), $reflectionClassController))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->then
                ->exception(function () use ($generator, $class) {
                    $generator->generate($class);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Class \'\\' . $class . '\' is final, unable to mock it')
            ->if($class = '\\' . uniqid())
            ->and($adapter->class_exists = function ($arg) use ($class) {
                return $arg === $class;
            })
            ->then
                ->exception(function () use ($generator, $class) {
                    $generator->generate($class);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Class \'' . $class . '\' is final, unable to mock it')
            ->if($reflectionClassController->isFinal = false)
            ->and($generator = new testedClass())
            ->then
                ->object($generator->generate(__CLASS__))->isIdenticalTo($generator)
                ->class('\mock\\' . __CLASS__)
                    ->hasParent(__CLASS__)
                    ->hasInterface(atoum\mock\aggregator::class)
            ->if($generator = new testedClass())
            ->and($generator->shunt('__construct'))
            ->then
                ->boolean($generator->isShunted('__construct'))->isTrue()
                ->object($generator->generate('reflectionMethod'))->isIdenticalTo($generator)
                ->boolean($generator->isShunted('__construct'))->isFalse()
            ->if($generator = new testedClass())
            ->and($generator->shuntParentClassCalls())
            ->then
                ->object($generator->generate('reflectionParameter'))->isIdenticalTo($generator)
                ->boolean($generator->callsToParentClassAreShunted())->isFalse()
        ;
    }

    public function testMethodIsMockable()
    {
        $this
            ->if($generator = new testedClass())
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

    public function testMethodIsMockableWithReservedWord($reservedWord)
    {
        $this
            ->if($generator = new testedClass())
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

    public function testGetMockedClassCodeWithOrphanizedMethod()
    {
        $this
            ->if->mockGenerator->orphanize('__construct')
            ->and($a = new \mock\reflectionParameter())
            ->and($this->calling($a)->getName = 'a')
            ->and($this->calling($a)->isPassedByReference = false)
            ->and($this->calling($a)->isDefaultValueAvailable = false)
            ->and($this->calling($a)->isOptional = false)
            ->and($this->calling($a)->isVariadic = false)
            ->and($this->calling($a)->allowsNull = true)
            ->and($b = new \mock\reflectionParameter())
            ->and($this->calling($b)->getName = 'b')
            ->and($this->calling($b)->isPassedByReference = false)
            ->and($this->calling($b)->isDefaultValueAvailable = false)
            ->and($this->calling($b)->isOptional = false)
            ->and($this->calling($b)->isVariadic = false)
            ->and($this->calling($b)->allowsNull = true)
            ->and($c = new \mock\reflectionParameter())
            ->and($this->calling($c)->getName = 'c')
            ->and($this->calling($c)->isPassedByReference = false)
            ->and($this->calling($c)->isDefaultValueAvailable = false)
            ->and($this->calling($c)->isOptional = false)
            ->and($this->calling($c)->isVariadic = false)
            ->and($this->calling($c)->allowsNull = true)
            ->and->mockGenerator->orphanize('__construct')
            ->and($constructor = new \mock\reflectionMethod())
            ->and($this->calling($constructor)->getName = '__construct')
            ->and($this->calling($constructor)->isConstructor = true)
            ->and($this->calling($constructor)->getParameters = [$a, $b, $c])
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
            ->and($this->calling($class)->getMethods = [$constructor])
            ->and($this->calling($class)->getConstructor = $constructor)
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use ($className) {
                return ($class == '\\' . $className);
            })
            ->and($generator = new testedClass())
            ->and($generator->setReflectionClassFactory(function () use ($class) {
                return $class;
            }))
            ->and($generator->setAdapter($adapter))
            ->and($analyzerController = new mock\controller())
            ->and($analyzerController->__construct = function () {
            })
            ->and($analyzerController->getTypeHintString[1] = 'string')
            ->and($analyzerController->getTypeHintString[2] = '')
            ->and($analyzerController->getTypeHintString[3] = '?int')
            ->and($analyzer = new \mock\atoum\atoum\tools\parameter\analyzer())
            ->and($generator->setParameterAnalyzer($analyzer))
            ->and($generator->orphanize('__construct'))
            ->then
                ->string($generator->getMockedClassCode($className))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $className . ' extends \\' . $className . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(string $a = null, $b = null, ?int $c = null, \atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array($a, $b, $c), array_slice(func_get_args(), 3, -1));' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export(['__construct'], true) . ';' . PHP_EOL .
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
            ->and($parameterController1 = new mock\controller())
            ->and($parameterController1->__construct = function () {
            })
            ->and($parameterController1->getName = 'arg1')
            ->and($parameterController1->isPassedByReference = false)
            ->and($parameterController1->isDefaultValueAvailable = false)
            ->and($parameterController1->isOptional = false)
            ->and($parameterController1->isVariadic = false)
            ->and($parameterController1->allowsNull = false)
            ->and($parameter1 = new \mock\reflectionParameter([\reflectionParameter::class, '__construct'], 0))
            ->and($parameterController2 = new mock\controller())
            ->and($parameterController2->__construct = function () {
            })
            ->and($parameterController2->getName = 'arg2')
            ->and($parameterController2->isPassedByReference = true)
            ->and($parameterController2->isDefaultValueAvailable = false)
            ->and($parameterController2->isOptional = false)
            ->and($parameterController2->isVariadic = false)
            ->and($parameterController2->allowsNull = false)
            ->and($parameter2 = new \mock\reflectionParameter([\reflectionParameter::class, '__construct'], 0))
            ->and($publicMethodController = new mock\controller())
            ->and($publicMethodController->__construct = function () {
            })
            ->and($publicMethodController->getName = $publicMethodName = uniqid())
            ->and($publicMethodController->isConstructor = false)
            ->and($publicMethodController->getParameters = [$parameter1, $parameter2])
            ->and($publicMethodController->isPublic = true)
            ->and($publicMethodController->isProtected = false)
            ->and($publicMethodController->isPrivate = false)
            ->and($publicMethodController->isFinal = false)
            ->and($publicMethodController->isStatic = false)
            ->and($publicMethodController->isAbstract = true)
            ->and($publicMethodController->returnsReference = false)
            ->and($publicMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '>=') ? $publicMethodController->hasTentativeReturnType = false : true)
            ->and($publicMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($protectedMethodController = new mock\controller())
            ->and($protectedMethodController->__construct = function () {
            })
            ->and($protectedMethodController->getName = $protectedMethodName = uniqid())
            ->and($protectedMethodController->isConstructor = false)
            ->and($protectedMethodController->getParameters = [])
            ->and($protectedMethodController->isPublic = false)
            ->and($protectedMethodController->isProtected = true)
            ->and($protectedMethodController->isPrivate = false)
            ->and($protectedMethodController->isFinal = false)
            ->and($protectedMethodController->isStatic = false)
            ->and($protectedMethodController->isAbstract = true)
            ->and($protectedMethodController->returnsReference = false)
            ->and($protectedMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '>=') ? $protectedMethodController->hasTentativeReturnType = false : true)
            ->and($protectedMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($classController = new mock\controller())
            ->and($classController->__construct = function () {
            })
            ->and($classController->getName = $className = uniqid())
            ->and($classController->isFinal = false)
            ->and($classController->isInterface = false)
            ->and($classController->getMethods = [$publicMethod, $protectedMethod])
            ->and($classController->getConstructor = null)
            ->and($classController->isAbstract = false)
            ->and($class = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($class) {
                return $class;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use ($className) {
                return ($class == '\\' . $className);
            })
            ->and($generator->setAdapter($adapter))
            ->and($analyzerController = new mock\controller())
            ->and($analyzerController->__construct = function () {
            })
            ->and($analyzerController->getTypeHintString[1] = '')
            ->and($analyzerController->getTypeHintString[2] = 'array')
            ->and($analyzer = new \mock\atoum\atoum\tools\parameter\analyzer())
            ->and($generator->setParameterAnalyzer($analyzer))
            ->then
                ->string($generator->getMockedClassCode($className))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $className . ' extends \\' . $className . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t\t" . '$this->getMockController()->' . $publicMethodName . ' = function() {' . PHP_EOL .
                    "\t\t\t" . '};' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . '$return = $this->getMockController()->invoke(\'' . $publicMethodName . '\', $arguments);' . PHP_EOL .
                    "\t\t" . 'return $return;' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'protected function ' . $protectedMethodName . '()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array(), array_slice(func_get_args(), 0));' . PHP_EOL .
                    "\t\t" . 'if (isset($this->getMockController()->' . $protectedMethodName . ') === false)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->getMockController()->' . $protectedMethodName . ' = function() {' . PHP_EOL .
                    "\t\t\t" . '};' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . '$return = $this->getMockController()->invoke(\'' . $protectedMethodName . '\', $arguments);' . PHP_EOL .
                    "\t\t" . 'return $return;' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct', $publicMethodName, $protectedMethodName], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
        ;
    }

    public function testGetMockedClassCodeForMethodWithTypeHint()
    {
        $this
            ->if($generator = new testedClass())
            ->and($parameterController1 = new mock\controller())
            ->and($parameterController1->__construct = function () {
            })
            ->and($parameterController1->getName = 'typeHint1')
            ->and($parameterController1->isPassedByReference = false)
            ->and($parameterController1->isDefaultValueAvailable = false)
            ->and($parameterController1->isOptional = false)
            ->and($parameterController1->isVariadic = false)
            ->and($parameterController1->allowsNull = false)
            ->and($parameter1 = new \mock\reflectionParameter([\reflectionParameter::class, '__construct'], 0))
            ->and($parameterController2 = new mock\controller())
            ->and($parameterController2->__construct = function () {
            })
            ->and($parameterController2->getName = 'typeHint2')
            ->and($parameterController2->isPassedByReference = true)
            ->and($parameterController2->isDefaultValueAvailable = false)
            ->and($parameterController2->isOptional = false)
            ->and($parameterController2->isVariadic = false)
            ->and($parameterController2->allowsNull = false)
            ->and($parameter2 = new \mock\reflectionParameter([\reflectionParameter::class, '__construct'], 0))
            ->and($parameterController3 = new mock\controller())
            ->and($parameterController3->__construct = function () {
            })
            ->and($parameterController3->getName = 'typeHint3')
            ->and($parameterController3->isPassedByReference = false)
            ->and($parameterController3->isDefaultValueAvailable = false)
            ->and($parameterController3->isOptional = false)
            ->and($parameterController3->isVariadic = false)
            ->and($parameterController3->allowsNull = false)
            ->and($parameter3 = new \mock\reflectionParameter([\reflectionParameter::class, '__construct'], 0))
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = uniqid())
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [$parameter1, $parameter2, $parameter3])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->and($analyzerController = new mock\controller())
            ->and($analyzerController->__construct = function () {
            })
            ->and($analyzerController->getTypeHintString[1] = 'string')
            ->and($analyzerController->getTypeHintString[2] = '\\Foo\\Bar')
            ->and($analyzerController->getTypeHintString[3] = '')
            ->and($analyzer = new \mock\atoum\atoum\tools\parameter\analyzer())
            ->and($generator->setParameterAnalyzer($analyzer))
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t" . 'public function ' . $methodName . '(string $typeHint1, \\Foo\\Bar & $typeHint2, $typeHint3)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array($typeHint1, & $typeHint2, $typeHint3), array_slice(func_get_args(), 3));' . PHP_EOL .
                    "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL .
                    "\t\t\t" . 'return $return;' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . 'else' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL .
                    "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                    "\t\t\t" . 'return $return;' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct', $methodName], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
        ;
    }

    public function testGetMockedClassCodeForMethodWithReturnType()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionTypeController = new mock\controller())
            ->and($reflectionTypeController->__construct = function () {
            })
            ->and($reflectionTypeController->isBuiltin = true)
            ->and($reflectionTypeController->allowsNull = false)
            ->and($reflectionTypeController->__toString = $returnType = 'string')
            ->and($reflectionType = new \mock\reflectionType())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = uniqid())
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = true)
            ->and($reflectionMethodController->getReturnType = $reflectionType)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t" . 'public function ' . $methodName . '(): ' . $returnType . PHP_EOL .
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
                    "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                    "\t\t\t" . 'return $return;' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct', $methodName], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
        ;
    }

    public function testGetMockedClassCodeForMethodWithReservedWord()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = 'list')
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = false)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                    "\t\t\t" . 'return $return;' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct', $methodName], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
        ;
    }

    public function testGetMockedClassCodeForMethodWithSelfReturnType()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionTypeController = new mock\controller())
            ->and($reflectionTypeController->__construct = function () {
            })
            ->and($reflectionTypeController->__toString = 'self')
            ->and($reflectionTypeController->isBuiltIn = false)
            ->and($reflectionTypeController->allowsNull = false)
            ->and($reflectionType = new \mock\reflectionType())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = 'returnSelf')
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = true)
            ->and($reflectionMethodController->getReturnType = $reflectionType)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($reflectionMethodController->getDeclaringClass = $reflectionClass)
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                'namespace mock {' . PHP_EOL .
                'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                '{' . PHP_EOL .
                $this->getMockControllerMethods() .
                "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                "\t\t" . '{' . PHP_EOL .
                "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                "\t" . 'public function ' . $methodName . '(): \\' . $realClass . PHP_EOL .
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
                "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                "\t\t\t" . 'return $return;' . PHP_EOL .
                "\t\t" . '}' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'return ' . var_export(['__construct', strtolower($methodName)], true) . ';' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                '}' . PHP_EOL .
                '}'
            )
        ;
    }

    public function testGetMockedClassCodeForMethodWithStaticReturnType()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionTypeController = new mock\controller())
            ->and($reflectionTypeController->__construct = function () {
            })
            ->and($reflectionTypeController->__toString = 'static')
            ->and($reflectionTypeController->isBuiltIn = false)
            ->and($reflectionTypeController->allowsNull = false)
            ->and($reflectionType = new \mock\reflectionType())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = 'returnStatic')
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = true)
            ->and($reflectionMethodController->getReturnType = $reflectionType)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($reflectionMethodController->getDeclaringClass = $reflectionClass)
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                'namespace mock {' . PHP_EOL .
                'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                '{' . PHP_EOL .
                $this->getMockControllerMethods() .
                "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                "\t\t" . '{' . PHP_EOL .
                "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                "\t" . 'public function ' . $methodName . '(): static' . PHP_EOL .
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
                "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                "\t\t\t" . 'return $return;' . PHP_EOL .
                "\t\t" . '}' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'return ' . var_export(['__construct', strtolower($methodName)], true) . ';' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                '}' . PHP_EOL .
                '}'
            )
        ;
    }

    /** @php >= 8.2 */
    public function testGetMockedClassCodeForMethodWithNullReturnType()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionTypeController = new mock\controller())
            ->and($reflectionTypeController->__construct = function () {
            })
            ->and($reflectionTypeController->__toString = 'null')
            ->and($reflectionTypeController->isBuiltIn = true)
            ->and($reflectionTypeController->allowsNull = true)
            ->and($reflectionType = new \mock\reflectionType())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = 'returnNull')
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = true)
            ->and($reflectionMethodController->getReturnType = $reflectionType)
            ->and($reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($reflectionMethodController->getDeclaringClass = $reflectionClass)
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                'namespace mock {' . PHP_EOL .
                'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                '{' . PHP_EOL .
                $this->getMockControllerMethods() .
                "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                "\t\t" . '{' . PHP_EOL .
                "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                "\t" . 'public function ' . $methodName . '(): null' . PHP_EOL .
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
                "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                "\t\t\t" . 'return $return;' . PHP_EOL .
                "\t\t" . '}' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'return ' . var_export(['__construct', strtolower($methodName)], true) . ';' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                '}' . PHP_EOL .
                '}'
            )
        ;
    }

    /** @php >= 8.2 */
    public function testGetMockedClassCodeForMethodWithNullableTrueReturnType()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionTypeController = new mock\controller())
            ->and($reflectionTypeController->__construct = function () {
            })
            ->and($reflectionTypeController->__toString = 'true')
            ->and($reflectionTypeController->isBuiltIn = true)
            ->and($reflectionTypeController->allowsNull = true)
            ->and($reflectionType = new \mock\reflectionType())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = 'returnNullableTrue')
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = true)
            ->and($reflectionMethodController->getReturnType = $reflectionType)
            ->and($reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($reflectionMethodController->getDeclaringClass = $reflectionClass)
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                'namespace mock {' . PHP_EOL .
                'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                '{' . PHP_EOL .
                $this->getMockControllerMethods() .
                "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                "\t\t" . '{' . PHP_EOL .
                "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                "\t" . 'public function ' . $methodName . '(): ?true' . PHP_EOL .
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
                "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                "\t\t\t" . 'return $return;' . PHP_EOL .
                "\t\t" . '}' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'return ' . var_export(['__construct', strtolower($methodName)], true) . ';' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                '}' . PHP_EOL .
                '}'
            )
        ;
    }

    /** @php >= 8.2 */
    public function testGetMockedClassCodeForMethodWithFalseReturnType()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionTypeController = new mock\controller())
            ->and($reflectionTypeController->__construct = function () {
            })
            ->and($reflectionTypeController->__toString = 'false')
            ->and($reflectionTypeController->isBuiltIn = true)
            ->and($reflectionTypeController->allowsNull = false)
            ->and($reflectionType = new \mock\reflectionType())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = 'returnFalse')
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = true)
            ->and($reflectionMethodController->getReturnType = $reflectionType)
            ->and($reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($reflectionMethodController->getDeclaringClass = $reflectionClass)
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                'namespace mock {' . PHP_EOL .
                'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                '{' . PHP_EOL .
                $this->getMockControllerMethods() .
                "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                "\t\t" . '{' . PHP_EOL .
                "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                "\t" . 'public function ' . $methodName . '(): false' . PHP_EOL .
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
                "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                "\t\t\t" . 'return $return;' . PHP_EOL .
                "\t\t" . '}' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'return ' . var_export(['__construct', strtolower($methodName)], true) . ';' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                '}' . PHP_EOL .
                '}'
            )
        ;
    }

    public function testGetMockedClassCodeForMethodWithUnionedReturnType()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionTypeController1 = new mock\controller())
            ->and($reflectionTypeController1->__construct = function () {
            })
            ->and($reflectionTypeController1->allowsNull = false)
            ->and($reflectionTypeController1->isBuiltin = true)
            ->and($reflectionTypeController1->getName = 'int')
            ->and($reflectionType1 = new \mock\reflectionNamedType())
            ->and($reflectionTypeController2 = new mock\controller())
            ->and($reflectionTypeController2->__construct = function () {
            })
            ->and($reflectionTypeController2->allowsNull = false)
            ->and($reflectionTypeController2->isBuiltin = false)
            ->and($reflectionTypeController2->getName = 'Mock\MyNumberObject')
            ->and($reflectionType2 = new \mock\reflectionNamedType())
            ->and($reflectionTypeController3 = new mock\controller())
            ->and($reflectionTypeController3->__construct = function () {
            })
            ->and($reflectionTypeController3->allowsNull = true)
            ->and($reflectionTypeController3->isBuiltin = true)
            ->and($reflectionTypeController3->getName = 'null')
            ->and($reflectionType3 = new \mock\reflectionNamedType())
            ->and($unionTypeController = new mock\controller())
            ->and($unionTypeController->getTypes = [$reflectionType1, $reflectionType2, $reflectionType3])
            ->and($unionTypeController->allowsNull = true)
            ->and($unionTypeController->__toString = 'int|Mock\MyNumberObject|null')
            ->and($unionType = new \mock\reflectionUnionType())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = 'returnUnion')
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = true)
            ->and($reflectionMethodController->getReturnType = $unionType)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($reflectionMethodController->getDeclaringClass = $reflectionClass)
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                'namespace mock {' . PHP_EOL .
                'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                '{' . PHP_EOL .
                $this->getMockControllerMethods() .
                "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                "\t\t" . '{' . PHP_EOL .
                "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                "\t" . 'public function ' . $methodName . '(): int|\Mock\MyNumberObject|null' . PHP_EOL .
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
                "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                "\t\t\t" . 'return $return;' . PHP_EOL .
                "\t\t" . '}' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'return ' . var_export(['__construct', strtolower($methodName)], true) . ';' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                '}' . PHP_EOL .
                '}'
            )
        ;
    }

    public function testGetMockedClassCodeForMethodWithMixedReturnType()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionTypeController = new mock\controller())
            ->and($reflectionTypeController->__construct = function () {
            })
            ->and($reflectionTypeController->allowsNull = false)
            ->and($reflectionTypeController->isBuiltin = true)
            ->and($reflectionTypeController->getName = 'mixed')
            ->and($reflectionType = new \mock\reflectionNamedType())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = 'returnUnion')
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = true)
            ->and($reflectionMethodController->getReturnType = $reflectionType)
            ->and(version_compare(phpversion(), '8.1', '<') ? true : $reflectionMethodController->hasTentativeReturnType = false)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($reflectionMethodController->getDeclaringClass = $reflectionClass)
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                'namespace mock {' . PHP_EOL .
                'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                '{' . PHP_EOL .
                $this->getMockControllerMethods() .
                "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                "\t\t" . '{' . PHP_EOL .
                "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                "\t" . 'public function ' . $methodName . '(): mixed' . PHP_EOL .
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
                "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                "\t\t\t" . 'return $return;' . PHP_EOL .
                "\t\t" . '}' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                "\t" . '{' . PHP_EOL .
                "\t\t" . 'return ' . var_export(['__construct', strtolower($methodName)], true) . ';' . PHP_EOL .
                "\t" . '}' . PHP_EOL .
                '}' . PHP_EOL .
                '}'
            )
        ;
    }

    public function testGenerateWithEachInstanceIsUnique()
    {
        $this
            ->if($generator = new testedClass())
            ->and($generator->eachInstanceIsUnique())
            ->then
                ->string($generator->getMockedClassCode(__NAMESPACE__ . '\mockable'))->isEqualTo(
                    'namespace mock\\' . __NAMESPACE__ . ' {' . PHP_EOL .
                    'final class mockable extends \\' . __NAMESPACE__ . '\mockable implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$this->{\'mock\' . uniqid()} = true;' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t\t" . 'return ' . var_export(['__construct'], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
        ;
    }

    /** @php >= 8.1 */
    public function testGetMockedClassCodeForMethodWithTentativeReturnType()
    {
        $this
            ->if($generator = new testedClass())
            ->and($reflectionTypeController = new mock\controller())
            ->and($reflectionTypeController->__construct = function () {
            })
            ->and($reflectionTypeController->isBuiltin = true)
            ->and($reflectionTypeController->allowsNull = false)
            ->and($reflectionTypeController->__toString = $returnType = 'string')
            ->and($reflectionType = new \mock\reflectionType())
            ->and($reflectionMethodController = new mock\controller())
            ->and($reflectionMethodController->__construct = function () {
            })
            ->and($reflectionMethodController->getName = $methodName = uniqid())
            ->and($reflectionMethodController->isConstructor = false)
            ->and($reflectionMethodController->getParameters = [])
            ->and($reflectionMethodController->isPublic = true)
            ->and($reflectionMethodController->isProtected = false)
            ->and($reflectionMethodController->isPrivate = false)
            ->and($reflectionMethodController->isFinal = false)
            ->and($reflectionMethodController->isStatic = false)
            ->and($reflectionMethodController->isAbstract = false)
            ->and($reflectionMethodController->returnsReference = false)
            ->and($reflectionMethodController->hasReturnType = false)
            ->and($reflectionMethodController->getReturnType = null)
            ->and($reflectionMethodController->hasTentativeReturnType = true)
            ->and($reflectionMethodController->getTentativeReturnType = $reflectionType)
            ->and($reflectionMethod = new \mock\reflectionMethod(uniqid(), uniqid()))
            ->and($reflectionClassController = new mock\controller())
            ->and($reflectionClassController->__construct = function () {
            })
            ->and($reflectionClassController->getName = function () use (& $realClass) {
                return $realClass;
            })
            ->and($reflectionClassController->isFinal = false)
            ->and($reflectionClassController->isInterface = false)
            ->and($reflectionClassController->getMethods = [$reflectionMethod])
            ->and($reflectionClassController->getConstructor = null)
            ->and($reflectionClassController->isAbstract = false)
            ->and($reflectionClass = new \mock\reflectionClass(uniqid()))
            ->and($generator->setReflectionClassFactory(function () use ($reflectionClass) {
                return $reflectionClass;
            }))
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->class_exists = function ($class) use (& $realClass) {
                return ($class == '\\' . $realClass);
            })
            ->and($generator->setAdapter($adapter))
            ->then
                ->string($generator->getMockedClassCode($realClass = uniqid()))->isEqualTo(
                    'namespace mock {' . PHP_EOL .
                    'final class ' . $realClass . ' extends \\' . $realClass . ' implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t" . 'public function ' . $methodName . '(): ' . $returnType . PHP_EOL .
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
                    "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL .
                    "\t\t\t" . 'return $return;' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct', $methodName], true) . ';' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    '}' . PHP_EOL .
                    '}'
                )
        ;
    }

    public function testGenerateUsingStrictTypes()
    {
        $this
            ->if($generator = new testedClass())
            ->and($generator->useStrictTypes())
            ->then
                ->string($generator->getMockedClassCode(__NAMESPACE__ . '\classWithScalarTypeHints'))->isEqualTo(
                    'declare(strict_types=1);' . PHP_EOL .
                    'namespace mock\\' . __NAMESPACE__ . ' {' . PHP_EOL .
                    'final class classWithScalarTypeHints extends \\' . __NAMESPACE__ . '\classWithScalarTypeHints implements \atoum\atoum\mock\aggregator' . PHP_EOL .
                    '{' . PHP_EOL .
                    $this->getMockControllerMethods() .
                    "\t" . 'public function __construct(\atoum\atoum\mock\controller $mockController = null)' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'if ($mockController === null)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
                    "\t" . 'public function foo(int $bar): int' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . '$arguments = array_merge(array($bar), array_slice(func_get_args(), 1));' . PHP_EOL .
                    "\t\t" . 'if (isset($this->getMockController()->foo) === true)' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$return = $this->getMockController()->invoke(\'foo\', $arguments);' . PHP_EOL .
                    "\t\t\t" . 'return $return;' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t\t" . 'else' . PHP_EOL .
                    "\t\t" . '{' . PHP_EOL .
                    "\t\t\t" . '$this->getMockController()->addCall(\'foo\', $arguments);' . PHP_EOL .
                    "\t\t\t" . '$return = call_user_func_array([parent::class, \'foo\'], $arguments);' . PHP_EOL .
                    "\t\t\t" . 'return $return;' . PHP_EOL .
                    "\t\t" . '}' . PHP_EOL .
                    "\t" . '}' . PHP_EOL .
                    "\t" . 'public static function getMockedMethods()' . PHP_EOL .
                    "\t" . '{' . PHP_EOL .
                    "\t\t" . 'return ' . var_export(['__construct', 'foo'], true) . ';' . PHP_EOL .
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
            "\t\t" . '$mockController = \atoum\atoum\mock\controller::getForMock($this);' . PHP_EOL .
            "\t\t" . 'if ($mockController === null)' . PHP_EOL .
            "\t\t" . '{' . PHP_EOL .
            "\t\t\t" . '$this->setMockController($mockController = new \atoum\atoum\mock\controller());' . PHP_EOL .
            "\t\t" . '}' . PHP_EOL .
            "\t\t" . 'return $mockController;' . PHP_EOL .
            "\t" . '}' . PHP_EOL .
            "\t" . 'public function setMockController(\atoum\atoum\mock\controller $controller)' . PHP_EOL .
            "\t" . '{' . PHP_EOL .
            "\t\t" . 'return $controller->control($this);' . PHP_EOL .
            "\t" . '}' . PHP_EOL .
            "\t" . 'public function resetMockController()' . PHP_EOL .
            "\t" . '{' . PHP_EOL .
            "\t\t" . '\atoum\atoum\mock\controller::getForMock($this)->reset();' . PHP_EOL .
            "\t\t" . 'return $this;' . PHP_EOL .
            "\t" . '}' . PHP_EOL
        ;
    }

    protected function testMethodIsMockableWithReservedWordDataProvider()
    {
        # See http://www.php.net/manual/en/reserved.keywords.php
        return [
            '__halt_compiler',
        ];
    }
}

class mockable
{
}

class foo
{
}

class classWithVariadicInConstructor
{
    public function __construct(foo... $foo)
    {
    }
}

class classWithScalarTypeHints
{
    public function foo(int $bar): int
    {
        return $bar * 2;
    }
}
