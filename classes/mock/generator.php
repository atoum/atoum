<?php

namespace mageekguy\atoum\mock;

use mageekguy\atoum;
use mageekguy\atoum\exceptions;

class generator
{
    const defaultNamespace = 'mock';

    protected $adapter = null;
    protected $reflectionClassFactory = null;
    protected $shuntedMethods = [];
    protected $overloadedMethods = [];
    protected $orphanizedMethods = [];
    protected $shuntParentClassCalls = false;
    protected $allowUndefinedMethodsUsage = true;
    protected $allIsInterface = false;
    protected $testedClass = '';
    protected $eachInstanceIsUnique = false;
    protected $useStrictTypes = false;

    private $defaultNamespace = null;

    public function __construct()
    {
        $this
            ->setAdapter()
            ->setReflectionClassFactory()
        ;
    }

    public function callsToParentClassAreShunted()
    {
        return $this->shuntParentClassCalls;
    }

    public function setAdapter(atoum\adapter $adapter = null)
    {
        $this->adapter = $adapter ?: new atoum\adapter();

        return $this;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setReflectionClassFactory(\closure $factory = null)
    {
        $this->reflectionClassFactory = $factory ?: function ($class) {
            return new \reflectionClass($class);
        };

        return $this;
    }

    public function getReflectionClassFactory()
    {
        return $this->reflectionClassFactory;
    }

    public function setDefaultNamespace($namespace)
    {
        $this->defaultNamespace = trim($namespace, '\\');

        return $this;
    }

    public function getDefaultNamespace()
    {
        return ($this->defaultNamespace === null ? self::defaultNamespace : $this->defaultNamespace);
    }

    public function overload(php\method $method)
    {
        $this->overloadedMethods[strtolower($method->getName())] = $method;

        return $this;
    }

    public function isOverloaded($method)
    {
        return ($this->getOverload($method) !== null);
    }

    public function getOverload($method)
    {
        return (isset($this->overloadedMethods[$method = strtolower($method)]) === false ? null : $this->overloadedMethods[$method]);
    }

    public function shunt($method)
    {
        if ($this->isShunted($method) === false) {
            $this->shuntedMethods[] = strtolower($method);
        }

        return $this;
    }

    public function isShunted($method)
    {
        return (in_array(strtolower($method), $this->shuntedMethods) === true);
    }

    public function shuntParentClassCalls()
    {
        $this->shuntParentClassCalls = true;

        return $this;
    }

    public function unshuntParentClassCalls()
    {
        $this->shuntParentClassCalls = false;

        return $this;
    }

    public function orphanize($method)
    {
        if ($this->isOrphanized($method) === false) {
            $this->orphanizedMethods[] = strtolower($method);
        }

        return $this->shunt($method);
    }

    public function isOrphanized($method)
    {
        return (in_array($method, $this->orphanizedMethods) === true);
    }

    public function allIsInterface()
    {
        $this->allIsInterface = true;

        return $this;
    }

    public function eachInstanceIsUnique()
    {
        $this->eachInstanceIsUnique = true;

        return $this;
    }

    public function useStrictTypes()
    {
        $this->useStrictTypes = true;

        return $this;
    }

    public function testedClassIs($testedClass)
    {
        $this->testedClass = strtolower($testedClass);

        return $this;
    }

    public function getMockedClassCode($class, $mockNamespace = null, $mockClass = null)
    {
        if (trim($class, '\\') == '' || rtrim($class, '\\') != $class) {
            throw new exceptions\runtime('Class name \'' . $class . '\' is invalid');
        }

        if ($mockNamespace === null) {
            $mockNamespace = $this->getNamespace($class);
        }

        $class = '\\' . ltrim($class, '\\');

        if ($mockClass === null) {
            $mockClass = self::getClassName($class);
        }

        if ($this->adapter->class_exists($mockNamespace . '\\' . $mockClass, false) === true || $this->adapter->interface_exists($mockNamespace . '\\' . $mockClass, false) === true) {
            throw new exceptions\logic('Class \'' . $mockNamespace . '\\' . $mockClass . '\' already exists');
        }

        if ($this->adapter->class_exists($class, true) === false && $this->adapter->interface_exists($class, true) === false) {
            $code = self::generateUnknownClassCode($mockNamespace, $mockClass, $this->eachInstanceIsUnique);
        } else {
            $reflectionClass = call_user_func($this->reflectionClassFactory, $class);

            if ($reflectionClass->isFinal() === true) {
                throw new exceptions\logic('Class \'' . $class . '\' is final, unable to mock it');
            }

            $code = $reflectionClass->isInterface() === false ? $this->generateClassCode($reflectionClass, $mockNamespace, $mockClass) : $this->generateInterfaceCode($reflectionClass, $mockNamespace, $mockClass);
        }

        $this->shuntedMethods = $this->overloadedMethods = $this->orphanizedMethods = [];

        $this->unshuntParentClassCalls();

        return $code;
    }

    public function generate($class, $mockNamespace = null, $mockClass = null)
    {
        eval($this->getMockedClassCode($class, $mockNamespace, $mockClass));

        return $this;
    }

    public function methodIsMockable(\reflectionMethod $method)
    {
        switch (true) {
            case $method->isFinal():
            case $method->isStatic():
            case static::methodNameIsReservedWord($method):
                return false;

            case $method->isPrivate():
            case $method->isProtected() && $method->isAbstract() === false:
                return $this->isOverloaded($method->getName());

            default:
                return true;
        }
    }

    public function disallowUndefinedMethodInInterface()
    {
        return $this->disallowUndefinedMethodUsage();
    }

    public function disallowUndefinedMethodUsage()
    {
        $this->allowUndefinedMethodsUsage = false;

        return $this;
    }

    public function allowUndefinedMethodInInterface()
    {
        return $this->allowUndefinedMethodUsage();
    }

    public function allowUndefinedMethodUsage()
    {
        $this->allowUndefinedMethodsUsage = true;

        return $this;
    }

    public function undefinedMethodInInterfaceAreAllowed()
    {
        return $this->undefinedMethodUsageIsAllowed();
    }

    public function undefinedMethodUsageIsAllowed()
    {
        return $this->allowUndefinedMethodsUsage === true;
    }

    protected function generateClassMethodCode(\reflectionClass $class)
    {
        $mockedMethods = '';
        $mockedMethodNames = [];
        $className = $class->getName();

        if ($this->allIsInterface && strtolower($className) != $this->testedClass) {
            foreach ($class->getMethods() as $method) {
                if ($this->methodIsMockable($method) === true) {
                    $this->orphanize($method->getName());
                }
            }
        }

        $constructor = $class->getConstructor();

        if ($constructor === null || $this->allIsInterface) {
            $mockedMethods .= self::generateDefaultConstructor(false, $this->eachInstanceIsUnique);
            $mockedMethodNames[] = '__construct';
        } elseif ($constructor->isFinal() === false) {
            $constructorName = $constructor->getName();

            $overload = $this->getOverload($constructorName);

            if ($constructor->isPublic() === false) {
                $this->shuntParentClassCalls();

                if ($overload === null) {
                    $this->overload(new php\method('__construct'));

                    $overload = $this->getOverload('__construct');
                }
            }

            $parameters = $this->getParameters($constructor);

            if ($overload === null) {
                $mockedMethods .= "\t" . 'public function __construct(' . $this->getParametersSignature($constructor) . ')';
            } else {
                $overload
                    ->addArgument(
                        php\method\argument::get('mockController')
                            ->isObject('\\' . __NAMESPACE__ . '\\controller')
                            ->setDefaultValue(null)
                    )
                ;

                $mockedMethods .= "\t" . $overload;
            }

            $mockedMethods .= PHP_EOL;
            $mockedMethods .= "\t" . '{' . PHP_EOL;

            if ($this->eachInstanceIsUnique === true) {
                $mockedMethods .= self::generateUniqueId();
            }

            if (self::hasVariadic($constructor) === true) {
                $mockedMethods .= "\t\t" . '$arguments = func_get_args();' . PHP_EOL;
                $mockedMethods .= "\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL;
            } else {
                $mockedMethods .= "\t\t" . '$arguments = array_merge(array(' . implode(', ', $parameters) . '), array_slice(func_get_args(), ' . count($parameters) . ', -1));' . PHP_EOL;
                $mockedMethods .= "\t\t" . 'if ($mockController === null)' . PHP_EOL;
                $mockedMethods .= "\t\t" . '{' . PHP_EOL;
                $mockedMethods .= "\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL;
                $mockedMethods .= "\t\t" . '}' . PHP_EOL;
            }

            $mockedMethods .= "\t\t" . 'if ($mockController !== null)' . PHP_EOL;
            $mockedMethods .= "\t\t" . '{' . PHP_EOL;
            $mockedMethods .= "\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL;
            $mockedMethods .= "\t\t" . '}' . PHP_EOL;

            if ($constructor->isAbstract() === true || $this->isShunted('__construct') === true || $this->isShunted($className) === true) {
                $methodName = ($this->isShunted($className) === true ? $className : '__construct');

                $mockedMethods .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === false)' . PHP_EOL;
                $mockedMethods .= "\t\t" . '{' . PHP_EOL;
                $mockedMethods .= "\t\t\t" . '$this->getMockController()->' . $methodName . ' = function() {};' . PHP_EOL;
                $mockedMethods .= "\t\t" . '}' . PHP_EOL;
                $mockedMethods .= "\t\t" . '$this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;
            } else {
                $mockedMethods .= "\t\t" . 'if (isset($this->getMockController()->' . $constructorName . ') === true)' . PHP_EOL;
                $mockedMethods .= "\t\t" . '{' . PHP_EOL;
                $mockedMethods .= "\t\t\t" . '$this->getMockController()->invoke(\'' . $constructorName . '\', $arguments);' . PHP_EOL;
                $mockedMethods .= "\t\t" . '}' . PHP_EOL;
                $mockedMethods .= "\t\t" . 'else' . PHP_EOL;
                $mockedMethods .= "\t\t" . '{' . PHP_EOL;
                $mockedMethods .= "\t\t\t" . '$this->getMockController()->addCall(\'' . $constructorName . '\', $arguments);' . PHP_EOL;

                if ($this->canCallParent()) {
                    $mockedMethods .= "\t\t\t" . 'call_user_func_array(\'parent::' . $constructorName . '\', $arguments);' . PHP_EOL;
                }

                $mockedMethods .= "\t\t" . '}' . PHP_EOL;
            }

            $mockedMethods .= "\t" . '}' . PHP_EOL;

            $mockedMethodNames[] = strtolower($constructorName);
        }

        foreach ($class->getMethods() as $method) {
            if ($method->isConstructor() === false && $this->methodIsMockable($method) === true) {
                $methodName = $method->getName();
                $mockedMethodNames[] = strtolower($methodName);
                $overload = $this->getOverload($methodName);
                $parameters = $this->getParameters($method);

                if ($overload !== null) {
                    $mockedMethods .= "\t" . $overload;
                } else {
                    $mockedMethods .= "\t" . $this->generateMethodSignature($method);
                }

                $mockedMethods .= PHP_EOL . "\t" . '{' . PHP_EOL;

                if (self::hasVariadic($method) === true) {
                    $mockedMethods .= "\t\t" . '$arguments = func_get_args();' . PHP_EOL;
                } else {
                    $mockedMethods .= "\t\t" . '$arguments = array_merge(array(' . implode(', ', $parameters) . '), array_slice(func_get_args(), ' . count($parameters) . '));' . PHP_EOL;
                }

                if ($this->isShunted($methodName) === true || $method->isAbstract() === true) {
                    $mockedMethods .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === false)' . PHP_EOL;
                    $mockedMethods .= "\t\t" . '{' . PHP_EOL;
                    $mockedMethods .= "\t\t\t" . '$this->getMockController()->' . $methodName . ' = function() {' . PHP_EOL;

                    if ($this->hasReturnType($method) === true && $this->isVoid($method) === false) {
                        $returnType = $this->getReflectionType($method);

                        switch (true) {
                            case (string) $returnType === 'self':
                            case (string) $returnType === 'parent':
                            case (string) $returnType === $class->getName():
                            case interface_exists((string) $returnType) && $class->implementsInterface((string) $returnType):
                                $mockedMethods .= "\t\t\t\t" . 'return $this;' . PHP_EOL;
                                break;

                            default:
                                $mockedMethods .= "\t\t\t\t" . 'return null;' . PHP_EOL;
                        }
                    }

                    $mockedMethods .= "\t\t\t" . '};' . PHP_EOL;
                    $mockedMethods .= "\t\t" . '}' . PHP_EOL;
                    $mockedMethods .= "\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;

                    if ($this->isVoid($method) === false) {
                        $mockedMethods .= "\t\t" . 'return $return;' . PHP_EOL;
                    }
                } else {
                    $mockedMethods .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL;
                    $mockedMethods .= "\t\t" . '{' . PHP_EOL;
                    $mockedMethods .= "\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;

                    if ($this->isVoid($method) === false) {
                        $mockedMethods .= "\t\t\t" . 'return $return;' . PHP_EOL;
                    }

                    $mockedMethods .= "\t\t" . '}' . PHP_EOL;
                    $mockedMethods .= "\t\t" . 'else' . PHP_EOL;
                    $mockedMethods .= "\t\t" . '{' . PHP_EOL;

                    if ($methodName === '__call') {
                        $mockedMethods .= "\t\t\t" . '$this->getMockController()->addCall(current(array_slice($arguments, 0, 1)), current(array_slice($arguments, 1)));' . PHP_EOL;
                    }

                    $mockedMethods .= "\t\t\t" . '$this->getMockController()->addCall(\'' . $methodName . '\', $arguments);' . PHP_EOL;

                    if ($this->canCallParent()) {
                        $mockedMethods .= "\t\t\t" . '$return = call_user_func_array(\'parent::' . $methodName . '\', $arguments);' . PHP_EOL;

                        if ($this->isVoid($method) === false) {
                            $mockedMethods .= "\t\t\t" . 'return $return;' . PHP_EOL;
                        }
                    } else {
                        if ($this->hasReturnType($method) === true && $this->isVoid($method) === false) {
                            $returnType = $this->getReflectionType($method);

                            switch (true) {
                                case (string) $returnType === 'self':
                                case (string) $returnType === 'parent':
                                case (string) $returnType === $class->getName():
                                case interface_exists((string) $returnType) && $class->implementsInterface((string) $returnType):
                                    $mockedMethods .= "\t\t\t" . 'return $this;' . PHP_EOL;
                                    break;

                                default:
                                    $mockedMethods .= "\t\t\t" . 'return null;' . PHP_EOL;
                            }
                        }
                    }

                    $mockedMethods .= "\t\t" . '}' . PHP_EOL;
                }

                $mockedMethods .= "\t" . '}' . PHP_EOL;
            }
        }

        if ($class->isAbstract() && $this->allowUndefinedMethodsUsage === true && in_array('__call', $mockedMethodNames) === false) {
            $mockedMethods .= self::generate__call();
            $mockedMethodNames[] = '__call';
        }

        return $mockedMethods . self::generateGetMockedMethod($mockedMethodNames);
    }

    protected function generateMethodSignature(\reflectionMethod $method)
    {
        return ($method->isPublic() === true ? 'public' : 'protected') . ' function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $method->getName() . '(' . $this->getParametersSignature($method) . ')' . $this->getReturnType($method);
    }

    protected function generateClassCode(\reflectionClass $class, $mockNamespace, $mockClass)
    {
        return ($this->useStrictTypes ? 'declare(strict_types=1);' . PHP_EOL : '') .
            'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
            'final class ' . $mockClass . ' extends \\' . $class->getName() . ' implements \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
            '{' . PHP_EOL .
            self::generateMockControllerMethods() .
            $this->generateClassMethodCode($class) .
            '}' . PHP_EOL .
            '}'
        ;
    }

    protected function generateInterfaceMethodCode(\reflectionClass $class, $addIteratorAggregate)
    {
        $mockedMethods = '';
        $mockedMethodNames = [];
        $hasConstructor = false;

        $methods = $class->getMethods(\reflectionMethod::IS_PUBLIC);

        if ($addIteratorAggregate === true) {
            $iteratorInterface = call_user_func($this->reflectionClassFactory, 'iteratorAggregate');

            $methods = array_merge($methods, $iteratorInterface->getMethods(\reflectionMethod::IS_PUBLIC));
        }

        foreach ($methods as $method) {
            $methodName = $method->getName();

            $mockedMethodNames[] = strtolower($methodName);

            $parameters = $this->getParameters($method);

            switch (true) {
                case $method->isFinal() === false && $method->isStatic() === false:
                    $isConstructor = $methodName === '__construct';

                    if ($isConstructor === true) {
                        $hasConstructor = true;
                    }

                    $methodCode = "\t" . 'public function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName . '(' . $this->getParametersSignature($method, $isConstructor) . ')' . $this->getReturnType($method) . PHP_EOL;
                    $methodCode .= "\t" . '{' . PHP_EOL;

                    if (self::hasVariadic($method) === true) {
                        $methodCode .= "\t\t" . '$arguments = func_get_args();' . PHP_EOL;
                    } else {
                        $methodCode .= "\t\t" . '$arguments = array_merge(array(' . implode(', ', $parameters) . '), array_slice(func_get_args(), ' . count($parameters) . ($isConstructor === false ? '' : ', -1') . '));' . PHP_EOL;
                    }

                    if ($isConstructor === true) {
                        if (self::hasVariadic($method) === true) {
                            $methodCode .= "\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL;
                        } else {
                            $methodCode .= "\t\t" . 'if ($mockController === null)' . PHP_EOL;
                            $methodCode .= "\t\t" . '{' . PHP_EOL;
                            $methodCode .= "\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL;
                            $methodCode .= "\t\t" . '}' . PHP_EOL;
                        }

                        $methodCode .= "\t\t" . 'if ($mockController !== null)' . PHP_EOL;
                        $methodCode .= "\t\t" . '{' . PHP_EOL;
                        $methodCode .= "\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL;
                        $methodCode .= "\t\t" . '}' . PHP_EOL;
                    }

                    $methodCode .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === false)' . PHP_EOL;
                    $methodCode .= "\t\t" . '{' . PHP_EOL;
                    $methodCode .= "\t\t\t" . '$this->getMockController()->' . $methodName . ' = function() {' . PHP_EOL;

                    if ($this->hasReturnType($method) === true && $this->isVoid($method) === false) {
                        $returnType = $this->getReflectionType($method);

                        switch (true) {
                            case (string) $returnType === 'self':
                            case (string) $returnType === 'parent':
                            case (string) $returnType === $class->getName():
                            case interface_exists((string) $returnType) && $class->implementsInterface((string) $returnType):
                                $methodCode .= "\t\t\t\t" . 'return $this;' . PHP_EOL;
                                break;

                            default:
                                $methodCode .= "\t\t\t\t" . 'return null;' . PHP_EOL;
                        }
                    }

                    $methodCode .= "\t\t\t" . '};' . PHP_EOL;
                    $methodCode .= "\t\t" . '}' . PHP_EOL;

                    if ($isConstructor === true) {
                        $methodCode .= "\t\t" . '$this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;
                    } else {
                        $methodCode .= "\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;

                        if ($this->isVoid($method) === false) {
                            $methodCode .= "\t\t" . 'return $return;' . PHP_EOL;
                        }
                    }
                    $methodCode .= "\t" . '}' . PHP_EOL;
                    break;

                case $method->isStatic() === true:
                    $methodCode = "\t" . 'public static function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName . '(' . $this->getParametersSignature($method) . ')' . PHP_EOL;
                    $methodCode .= "\t" . '{' . PHP_EOL;
                    $methodCode .= "\t\t" . '$arguments = array_merge(array(' . implode(', ', $parameters) . '), array_slice(func_get_args(), ' . count($parameters) . ', -1));' . PHP_EOL;

                    if ($this->isVoid($method) === false) {
                        $methodCode .= "\t\t" . 'return call_user_func_array(array(\'parent\', \'' . $methodName . '\'), $arguments);' . PHP_EOL;
                    }

                    $methodCode .= "\t" . '}' . PHP_EOL;
                    break;

                default:
                    $methodCode = '';
            }

            $mockedMethods .= $methodCode;
        }

        if ($hasConstructor === false) {
            $mockedMethods .= self::generateDefaultConstructor(false, $this->eachInstanceIsUnique);
            $mockedMethodNames[] = '__construct';
        }

        if ($this->allowUndefinedMethodsUsage === true) {
            $mockedMethods .= self::generate__call();
            $mockedMethodNames[] = '__call';
        }

        $mockedMethods .= self::generateGetMockedMethod($mockedMethodNames);

        return $mockedMethods;
    }

    protected function generateInterfaceCode(\reflectionClass $class, $mockNamespace, $mockClass)
    {
        $addIteratorAggregate = (
                $class->isInstantiable() === false
            && (
                    $class->implementsInterface('traversable') === true
                && $class->implementsInterface('iterator') === false
                && $class->implementsInterface('iteratorAggregate') === false
            )
        );

        return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
            'final class ' . $mockClass . ' implements \\' . ($addIteratorAggregate === false ? '' : 'iteratorAggregate, \\') . $class->getName() . ', \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
            '{' . PHP_EOL .
            self::generateMockControllerMethods() .
            $this->generateInterfaceMethodCode($class, $addIteratorAggregate) .
            '}' . PHP_EOL .
            '}'
        ;
    }

    protected function getNamespace($class)
    {
        $class = ltrim($class, '\\');
        $lastAntiSlash = strrpos($class, '\\');

        return '\\' . $this->getDefaultNamespace() . ($lastAntiSlash === false ? '' : '\\' . substr($class, 0, $lastAntiSlash));
    }

    protected function getReturnType(\reflectionMethod $method)
    {
        $returnTypeCode = '';

        if ($method->getName() === '__construct' || $this->hasReturnType($method) === false) {
            return $returnTypeCode;
        }

        $returnType = $this->getReflectionType($method);
        $isNullable = $this->isNullable($returnType);

        switch (true) {
            case (string) $returnType === 'self':
                $returnTypeCode = ': ' . ($isNullable ? '?' : '') . '\\' . $method->getDeclaringClass()->getName();
                break;

            case (string) $returnType === 'parent':
                $returnTypeCode = ': ' . ($isNullable ? '?' : '') . '\\' . $method->getDeclaringClass()->getParentClass()->getName();
                break;

            case $returnType->isBuiltin():
                $returnTypeCode = ': ' . ($isNullable ? '?' : '') . $returnType;
                break;

            default:
                $returnTypeCode = ': ' . ($isNullable ? '?' : '') . '\\' . $returnType;
        }

        return $returnTypeCode;
    }

    protected function isNullable(\reflectionType $type)
    {
        return version_compare(PHP_VERSION, '7.0') >= 0 && $type->allowsNull() === true;
    }

    protected function hasReturnType(\reflectionMethod $method)
    {
        return version_compare(PHP_VERSION, '7.0') >= 0 && $method->hasReturnType() === true;
    }

    protected function getReflectionType(\reflectionMethod $method)
    {
        return $this->hasReturnType($method) ? $method->getReturnType() : null;
    }

    protected function isVoid(\reflectionMethod $method)
    {
        return $this->hasReturnType($method) ? (string) $method->getReturnType() === 'void' : false;
    }

    protected static function isNullableParameter(\ReflectionParameter $parameter)
    {
        return version_compare(PHP_VERSION, '7.1') >= 0 &&
               $parameter->allowsNull() &&
               (!$parameter->isDefaultValueAvailable() || ($parameter->isDefaultValueAvailable() && null !== $parameter->getDefaultValue()));
    }

    protected static function isDefaultParameterNull(\ReflectionParameter $parameter)
    {
        return $parameter->allowsNull() &&
               $parameter->isDefaultValueAvailable() &&
               null === $parameter->getDefaultValue();
    }

    protected function getParameters(\reflectionMethod $method)
    {
        $parameters = [];

        $overload = $this->getOverload($method->getName());

        if ($overload === null) {
            foreach ($method->getParameters() as $parameter) {
                $parameters[] = ($parameter->isPassedByReference() === false ? '' : '& ') . '$' . $parameter->getName();
            }
        } else {
            foreach ($overload->getArguments() as $argument) {
                $parameters[] = $argument->getVariable();
            }
        }

        return $parameters;
    }

    protected function getParametersSignature(\reflectionMethod $method, $forceMockController = false)
    {
        $parameters = [];

        $mustBeNull = $this->isOrphanized($method->getName());

        foreach ($method->getParameters() as $parameter) {
            $parameterCode = self::getParameterType($parameter) . ($parameter->isPassedByReference() == false ? '' : '& ') . ($parameter->isVariadic() == false ? '' : '... ') . '$' . $parameter->getName();

            switch (true) {
                case $parameter->isDefaultValueAvailable():
                    $parameterCode .= ' = ' . var_export($parameter->getDefaultValue(), true);
                    break;

                case self::isDefaultParameterNull($parameter):
                case $parameter->isOptional() && $parameter->isVariadic() == false:
                case $mustBeNull:
                    $parameterCode .= ' = null';
            }

            $parameters[] = $parameterCode;
        }

        if (self::hasVariadic($method) === false && ($method->isConstructor() || $forceMockController)) {
            $parameters[] = '\\' . __NAMESPACE__ . '\\controller $mockController = null';
        }

        return implode(', ', $parameters);
    }

    protected function canCallParent()
    {
        return $this->shuntParentClassCalls === false && $this->allIsInterface === false;
    }

    protected static function getClassName($class)
    {
        $class = ltrim($class, '\\');
        $lastAntiSlash = strrpos($class, '\\');

        return ($lastAntiSlash === false ? $class : substr($class, $lastAntiSlash + 1));
    }

    protected static function getParameterType(\reflectionParameter $parameter)
    {
        $prefix = self::isNullableParameter($parameter) ? '?' : '';
        switch (true) {
            case $parameter->isArray():
                return $prefix . 'array ';

            case method_exists($parameter, 'isCallable') && $parameter->isCallable():
                return $prefix . 'callable ';

            case ($class = $parameter->getClass()):
                return $prefix . '\\' . $class->getName() . ' ';

            case method_exists($parameter, 'hasType') && $parameter->hasType():
                return $prefix . $parameter->getType() . ' ';

            default:
                return '';
        }
    }

    protected static function hasVariadic(\reflectionMethod $method)
    {
        $parameters = $method->getParameters();

        if (count($parameters) === 0) {
            return false;
        }

        return end($parameters)->isVariadic();
    }

    protected static function generateMockControllerMethods()
    {
        return
            "\t" . 'public function getMockController()' . PHP_EOL .
            "\t" . '{' . PHP_EOL .
            "\t\t" . '$mockController = \mageekguy\atoum\mock\controller::getForMock($this);' . PHP_EOL .
            "\t\t" . 'if ($mockController === null)' . PHP_EOL .
            "\t\t" . '{' . PHP_EOL .
            "\t\t\t" . '$this->setMockController($mockController = new \\' . __NAMESPACE__ . '\\controller());' . PHP_EOL .
            "\t\t" . '}' . PHP_EOL .
            "\t\t" . 'return $mockController;' . PHP_EOL .
            "\t" . '}' . PHP_EOL .
            "\t" . 'public function setMockController(\\' . __NAMESPACE__ . '\\controller $controller)' . PHP_EOL .
            "\t" . '{' . PHP_EOL .
            "\t\t" . 'return $controller->control($this);' . PHP_EOL .
            "\t" . '}' . PHP_EOL .
            "\t" . 'public function resetMockController()' . PHP_EOL .
            "\t" . '{' . PHP_EOL .
            "\t\t" . '\mageekguy\atoum\mock\controller::getForMock($this)->reset();' . PHP_EOL .
            "\t\t" . 'return $this;' . PHP_EOL .
            "\t" . '}' . PHP_EOL
        ;
    }

    protected static function generateDefaultConstructor($disableMethodChecking = false, $uniqueId = false)
    {
        $defaultConstructor =
            "\t" . 'public function __construct(\\' . __NAMESPACE__ . '\\controller $mockController = null)' . PHP_EOL .
            "\t" . '{' . PHP_EOL;

        if ($uniqueId === true) {
            $defaultConstructor .= self::generateUniqueId();
        }

        $defaultConstructor .=
            "\t\t" . 'if ($mockController === null)' . PHP_EOL .
            "\t\t" . '{' . PHP_EOL .
            "\t\t\t" . '$mockController = \mageekguy\atoum\mock\controller::get();' . PHP_EOL .
            "\t\t" . '}' . PHP_EOL .
            "\t\t" . 'if ($mockController !== null)' . PHP_EOL .
            "\t\t" . '{' . PHP_EOL .
            "\t\t\t" . '$this->setMockController($mockController);' . PHP_EOL .
            "\t\t" . '}' . PHP_EOL
        ;

        if ($disableMethodChecking === true) {
            $defaultConstructor .= "\t\t" . '$this->getMockController()->disableMethodChecking();' . PHP_EOL;
        }

        $defaultConstructor .=
            "\t\t" . 'if (isset($this->getMockController()->__construct) === true)' . PHP_EOL .
            "\t\t" . '{' . PHP_EOL .
            "\t\t\t" . '$this->getMockController()->invoke(\'__construct\', func_get_args());' . PHP_EOL .
            "\t\t" . '}' . PHP_EOL .
            "\t" . '}' . PHP_EOL
        ;

        return $defaultConstructor;
    }

    protected static function generate__call()
    {
        return
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
            "\t" . '}' . PHP_EOL
        ;
    }

    protected static function generateGetMockedMethod(array $mockedMethodNames)
    {
        return
            "\t" . 'public static function getMockedMethods()' . PHP_EOL .
            "\t" . '{' . PHP_EOL .
            "\t\t" . 'return ' . var_export($mockedMethodNames, true) . ';' . PHP_EOL .
            "\t" . '}' . PHP_EOL
        ;
    }

    protected static function generateUnknownClassCode($mockNamespace, $mockClass, $uniqueId = false, $useStrictTypes = false)
    {
        return ($useStrictTypes ? 'declare(strict_types=1);' . PHP_EOL  : '') .
            'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
            'final class ' . $mockClass . ' implements \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
            '{' . PHP_EOL .
            self::generateMockControllerMethods() .
            self::generateDefaultConstructor(true, $uniqueId) .
            self::generate__call() .
            self::generateGetMockedMethod(['__call']) .
            '}' . PHP_EOL .
            '}'
        ;
    }

    protected static function methodNameIsReservedWord(\reflectionMethod $method)
    {
        return in_array($method->getName(), self::getMethodNameReservedWordByVersion(), true);
    }

    protected static function getMethodNameReservedWordByVersion()
    {
        if (PHP_MAJOR_VERSION >= 7) {
            return ['__halt_compiler'];
        }

        return [
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
            'xor',
        ];
    }

    private static function generateUniqueId()
    {
        return "\t\t" . '$this->{\'mock\' . uniqid()} = true;' . PHP_EOL;
    }
}
