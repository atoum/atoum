<?php

namespace atoum\atoum\mock;

use atoum\atoum;
use atoum\atoum\exceptions;

class generator
{
    public const defaultNamespace = 'mock';

    protected ?atoum\adapter $adapter = null;
    protected ?atoum\tools\parameter\analyzer $parameterAnalyzer = null;
    protected ?\Closure $reflectionClassFactory = null;
    protected array $shuntedMethods = [];
    protected array $overloadedMethods = [];
    protected array $orphanizedMethods = [];
    protected bool $shuntParentClassCalls = false;
    protected bool $allowUndefinedMethodsUsage = true;
    protected bool $allIsInterface = false;
    protected string $testedClass = '';
    protected bool $eachInstanceIsUnique = false;
    protected bool $useStrictTypes = false;

    private ?string $defaultNamespace = null;

    public function __construct()
    {
        $this
            ->setAdapter()
            ->setParameterAnalyzer()
            ->setReflectionClassFactory()
        ;
    }

    public function callsToParentClassAreShunted(): bool
    {
        return $this->shuntParentClassCalls;
    }

    public function setAdapter(?atoum\adapter $adapter = null): static
    {
        $this->adapter = $adapter ?: new atoum\adapter();

        return $this;
    }

    public function getAdapter(): atoum\adapter
    {
        return $this->adapter;
    }

    public function setParameterAnalyzer(?atoum\tools\parameter\analyzer $analyzer = null): static
    {
        $this->parameterAnalyzer = $analyzer ?: new atoum\tools\parameter\analyzer();

        return $this;
    }

    public function getParameterAnalyzer(): atoum\tools\parameter\analyzer
    {
        return $this->parameterAnalyzer;
    }

    public function setReflectionClassFactory(?\Closure $factory = null): static
    {
        $this->reflectionClassFactory = $factory ?: function ($class) {
            return new \reflectionClass($class);
        };

        return $this;
    }

    public function getReflectionClassFactory(): \Closure
    {
        return $this->reflectionClassFactory;
    }

    public function setDefaultNamespace(string $namespace): static
    {
        $this->defaultNamespace = trim($namespace, '\\');

        return $this;
    }

    public function getDefaultNamespace(): string
    {
        return ($this->defaultNamespace === null ? self::defaultNamespace : $this->defaultNamespace);
    }

    public function overload(php\method $method): static
    {
        $this->overloadedMethods[strtolower($method->getName())] = $method;

        return $this;
    }

    public function isOverloaded(string $method): bool
    {
        return ($this->getOverload($method) !== null);
    }

    public function getOverload(string $method): ?php\method
    {
        return (isset($this->overloadedMethods[$method = strtolower($method)]) === false ? null : $this->overloadedMethods[$method]);
    }

    public function shunt(string $method): static
    {
        if ($this->isShunted($method) === false) {
            $this->shuntedMethods[] = strtolower($method);
        }

        return $this;
    }

    public function isShunted(string $method): bool
    {
        return (in_array(strtolower($method), $this->shuntedMethods) === true);
    }

    public function shuntParentClassCalls(): static
    {
        $this->shuntParentClassCalls = true;

        return $this;
    }

    public function unshuntParentClassCalls(): static
    {
        $this->shuntParentClassCalls = false;

        return $this;
    }

    public function orphanize(string $method): static
    {
        if ($this->isOrphanized($method) === false) {
            $this->orphanizedMethods[] = strtolower($method);
        }

        return $this->shunt($method);
    }

    public function isOrphanized(string $method): bool
    {
        return (in_array($method, $this->orphanizedMethods) === true);
    }

    public function allIsInterface(): static
    {
        $this->allIsInterface = true;

        return $this;
    }

    public function eachInstanceIsUnique(): static
    {
        $this->eachInstanceIsUnique = true;

        return $this;
    }

    public function useStrictTypes(): static
    {
        $this->useStrictTypes = true;

        return $this;
    }

    public function testedClassIs(string $testedClass): static
    {
        $this->testedClass = strtolower($testedClass);

        return $this;
    }

    public function getMockedClassCode(string $class, ?string $mockNamespace = null, ?string $mockClass = null): string
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

    public function generate(string $class, ?string $mockNamespace = null, ?string $mockClass = null): static
    {
        eval($this->getMockedClassCode($class, $mockNamespace, $mockClass));

        return $this;
    }

    public function methodIsMockable(\reflectionMethod $method): bool
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

    public function disallowUndefinedMethodInInterface(): static
    {
        return $this->disallowUndefinedMethodUsage();
    }

    public function disallowUndefinedMethodUsage(): static
    {
        $this->allowUndefinedMethodsUsage = false;

        return $this;
    }

    public function allowUndefinedMethodInInterface(): static
    {
        return $this->allowUndefinedMethodUsage();
    }

    public function allowUndefinedMethodUsage(): static
    {
        $this->allowUndefinedMethodsUsage = true;

        return $this;
    }

    public function undefinedMethodInInterfaceAreAllowed(): bool
    {
        return $this->undefinedMethodUsageIsAllowed();
    }

    public function undefinedMethodUsageIsAllowed(): bool
    {
        return $this->allowUndefinedMethodsUsage === true;
    }

    protected function generateClassMethodCode(\reflectionClass $class): string
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
                $mockedMethods .= "\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL;
            } else {
                $mockedMethods .= "\t\t" . '$arguments = array_merge(array(' . implode(', ', $parameters) . '), array_slice(func_get_args(), ' . count($parameters) . ', -1));' . PHP_EOL;
                $mockedMethods .= "\t\t" . 'if ($mockController === null)' . PHP_EOL;
                $mockedMethods .= "\t\t" . '{' . PHP_EOL;
                $mockedMethods .= "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL;
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
                    $mockedMethods .= "\t\t\t" . 'call_user_func_array([parent::class, \'' . $constructorName . '\'], $arguments);' . PHP_EOL;
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
                        $returnTypeName = $this->getReflectionTypeName($returnType);
                        $allowsNull = $returnType->allowsNull();

                        switch (true) {
                            case $returnTypeName === 'self':
                            case $returnTypeName === 'static':
                            case $returnTypeName === 'parent':
                            case $returnTypeName === $class->getName():
                            case interface_exists($returnTypeName) && $class->implementsInterface($returnTypeName):
                                $mockedMethods .= "\t\t\t\t" . 'return $this;' . PHP_EOL;
                                break;

                            case $returnTypeName === 'bool':
                                $mockedMethods .= "\t\t\t\t" . 'return false;' . PHP_EOL;
                                break;

                            case $returnTypeName === 'int':
                                $mockedMethods .= "\t\t\t\t" . 'return 0;' . PHP_EOL;
                                break;

                            case $returnTypeName === 'float':
                                $mockedMethods .= "\t\t\t\t" . 'return 0.0;' . PHP_EOL;
                                break;

                            case $returnTypeName === 'string':
                                $mockedMethods .= "\t\t\t\t" . 'return \'\';' . PHP_EOL;
                                break;

                            case $returnTypeName === 'array':
                                $mockedMethods .= "\t\t\t\t" . 'return [];' . PHP_EOL;
                                break;

                            case $returnTypeName === 'mixed':
                            case $allowsNull:
                                $mockedMethods .= "\t\t\t\t" . 'return null;' . PHP_EOL;
                                break;

                            default:
                                // Pour les objets non-nullables, retourner null (ce sera une erreur, mais explicite)
                                $mockedMethods .= "\t\t\t\t" . 'return null;' . PHP_EOL;
                        }
                    }

                    $mockedMethods .= "\t\t\t" . '};' . PHP_EOL;
                    $mockedMethods .= "\t\t" . '}' . PHP_EOL;
                    $mockedMethods .= "\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;

                    if ($this->isVoid($method) === false) {
                        // If invoke() returns null but the return type is non-nullable, use default value
                        if ($this->hasReturnType($method) === true) {
                            $returnType = $this->getReflectionType($method);
                            if ($returnType !== null && $returnType->allowsNull() === false) {
                                $defaultValue = $this->getDefaultReturnValue($returnType, $method, $class);
                                if ($defaultValue !== '') {
                                    $mockedMethods .= "\t\t" . 'if ($return === null) {' . PHP_EOL;
                                    $mockedMethods .= "\t\t\t" . 'return ' . $defaultValue . ';' . PHP_EOL;
                                    $mockedMethods .= "\t\t" . '}' . PHP_EOL;
                                }
                            }
                        }
                        $mockedMethods .= "\t\t" . 'return $return;' . PHP_EOL;
                    }
                } else {
                    $mockedMethods .= "\t\t" . 'if (isset($this->getMockController()->' . $methodName . ') === true)' . PHP_EOL;
                    $mockedMethods .= "\t\t" . '{' . PHP_EOL;
                    $mockedMethods .= "\t\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;

                    if ($this->isVoid($method) === false) {
                        // If invoke() returns null but the return type is non-nullable, use default value
                        if ($this->hasReturnType($method) === true) {
                            $returnType = $this->getReflectionType($method);
                            if ($returnType !== null && $returnType->allowsNull() === false) {
                                $defaultValue = $this->getDefaultReturnValue($returnType, $method, $class);
                                if ($defaultValue !== '') {
                                    $mockedMethods .= "\t\t\t" . 'if ($return === null) {' . PHP_EOL;
                                    $mockedMethods .= "\t\t\t\t" . 'return ' . $defaultValue . ';' . PHP_EOL;
                                    $mockedMethods .= "\t\t\t" . '}' . PHP_EOL;
                                }
                            }
                        }
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
                        $mockedMethods .= "\t\t\t" . '$return = call_user_func_array([parent::class, \'' . $methodName . '\'], $arguments);' . PHP_EOL;

                        if ($this->isVoid($method) === false) {
                            $mockedMethods .= "\t\t\t" . 'return $return;' . PHP_EOL;
                        }
                    } else {
                        if ($this->hasReturnType($method) === true && $this->isVoid($method) === false) {
                            $returnType = $this->getReflectionType($method);
                            $returnTypeName = $this->getReflectionTypeName($returnType);

                            switch (true) {
                                case $returnTypeName === 'self':
                                case $returnTypeName === 'static':
                                case $returnTypeName === 'parent':
                                case $returnTypeName === $class->getName():
                                case interface_exists($returnTypeName) && $class->implementsInterface($returnTypeName):
                                    $mockedMethods .= "\t\t\t" . 'return $this;' . PHP_EOL;
                                    break;

                                default:
                                    $defaultValue = $this->getDefaultReturnValue($returnType, $method, $class);
                                    if ($defaultValue !== '') {
                                        $mockedMethods .= "\t\t\t" . 'return ' . $defaultValue . ';' . PHP_EOL;
                                    }
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

    protected function generateMethodSignature(\reflectionMethod $method): string
    {
        return ($method->isPublic() === true ? 'public' : 'protected') . ' function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $method->getName() . '(' . $this->getParametersSignature($method) . ')' . $this->getReturnType($method);
    }

    protected function generateClassCode(\reflectionClass $class, string $mockNamespace, string $mockClass): string
    {
        // PHP 8.2+ : Check if class is readonly
        $classModifiers = 'final ';
        if (version_compare(PHP_VERSION, '8.2.0', '>=') && $class->isReadOnly()) {
            $classModifiers .= 'readonly ';
        }

        return ($this->useStrictTypes ? 'declare(strict_types=1);' . PHP_EOL : '') .
            'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
            $classModifiers . 'class ' . $mockClass . ' extends \\' . $class->getName() . ' implements \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
            '{' . PHP_EOL .
            self::generateMockControllerMethods() .
            $this->generatePromotedProperties($class) .
            $this->generatePropertiesWithHooks($class) .
            $this->generatePropertiesWithAsymmetricVisibility($class) .
            $this->generateClassMethodCode($class) .
            '}' . PHP_EOL .
            '}'
        ;
    }

    protected function generateInterfaceMethodCode(\reflectionClass $class, bool $addIteratorAggregate): string
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
                            $methodCode .= "\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL;
                        } else {
                            $methodCode .= "\t\t" . 'if ($mockController === null)' . PHP_EOL;
                            $methodCode .= "\t\t" . '{' . PHP_EOL;
                            $methodCode .= "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL;
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
                        $returnTypeName = $this->getReflectionTypeName($returnType);

                        switch (true) {
                            case $returnTypeName === 'self':
                            case $returnTypeName === 'static':
                            case $returnTypeName === 'parent':
                            case $returnTypeName === $class->getName():
                            case interface_exists($returnTypeName) && $class->implementsInterface($returnTypeName):
                                $methodCode .= "\t\t\t\t" . 'return $this;' . PHP_EOL;
                                break;

                            default:
                                $defaultValue = $this->getDefaultReturnValue($returnType, $method, $class);
                                if ($defaultValue !== '') {
                                    $methodCode .= "\t\t\t\t" . 'return ' . $defaultValue . ';' . PHP_EOL;
                                }
                        }
                    }

                    $methodCode .= "\t\t\t" . '};' . PHP_EOL;
                    $methodCode .= "\t\t" . '}' . PHP_EOL;

                    if ($isConstructor === true) {
                        $methodCode .= "\t\t" . '$this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;
                    } else {
                        $methodCode .= "\t\t" . '$return = $this->getMockController()->invoke(\'' . $methodName . '\', $arguments);' . PHP_EOL;

                        if ($this->isVoid($method) === false) {
                            // If invoke() returns null but the return type is non-nullable, use default value
                            if ($this->hasReturnType($method) === true) {
                                $returnType = $this->getReflectionType($method);
                                if ($returnType !== null && $returnType->allowsNull() === false) {
                                    $defaultValue = $this->getDefaultReturnValue($returnType, $method, $class);
                                    if ($defaultValue !== '') {
                                        $methodCode .= "\t\t" . 'if ($return === null) {' . PHP_EOL;
                                        $methodCode .= "\t\t\t" . 'return ' . $defaultValue . ';' . PHP_EOL;
                                        $methodCode .= "\t\t" . '}' . PHP_EOL;
                                    }
                                }
                            }
                            $methodCode .= "\t\t" . 'return $return;' . PHP_EOL;
                        }
                    }
                    $methodCode .= "\t" . '}' . PHP_EOL;
                    break;

                case $method->isStatic() === true:
                    $methodCode = "\t" . 'public static function' . ($method->returnsReference() === false ? '' : ' &') . ' ' . $methodName . '(' . $this->getParametersSignature($method) . ')' . $this->getReturnType($method) . PHP_EOL;
                    $methodCode .= "\t" . '{' . PHP_EOL;
                    $methodCode .= "\t\t" . '$arguments = array_merge(array(' . implode(', ', $parameters) . '), array_slice(func_get_args(), ' . count($parameters) . ', -1));' . PHP_EOL;

                    if ($this->isVoid($method) === false) {
                        if ($class->getParentClass() !== false) {
                            $methodCode .= "\t\treturn call_user_func_array([parent::class, '" . $methodName . "'], $arguments);" . PHP_EOL;
                        } elseif ($this->hasReturnType($method) === true) {
                            $returnType = $this->getReflectionType($method);
                            if ($returnType !== null) {
                                $defaultValue = $this->getDefaultReturnValue($returnType, $method, $class, true);
                                if ($defaultValue !== '') {
                                    $methodCode .= "\t\treturn " . $defaultValue . ';' . PHP_EOL;
                                }
                            }
                        }
                    } elseif ($class->getParentClass() !== false) {
                        $methodCode .= "\t\tcall_user_func_array([parent::class, '" . $methodName . "'], $arguments);" . PHP_EOL;
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

    protected function getNamespace(string $class): string
    {
        $class = ltrim($class, '\\');
        $lastAntiSlash = strrpos($class, '\\');

        return '\\' . $this->getDefaultNamespace() . ($lastAntiSlash === false ? '' : '\\' . substr($class, 0, $lastAntiSlash));
    }

    protected function getReturnType(\reflectionMethod $method): string
    {
        $returnTypeCode = '';

        if ($method->getName() === '__construct' || $this->hasReturnType($method) === false) {
            return $returnTypeCode;
        }

        $returnType = $this->getReflectionType($method);
        $returnTypeName = $this->getReflectionTypeName($returnType);
        $isNullable = $returnType->allowsNull() === true;

        // Handle special cases: self, parent, static
        if ($returnType instanceof \reflectionNamedType) {
            switch ($returnTypeName) {
                case 'self':
                    return ': ' . ($isNullable ? '?' : '') . '\\' . $method->getDeclaringClass()->getName();

                case 'parent':
                    return ': ' . ($isNullable ? '?' : '') . '\\' . $method->getDeclaringClass()->getParentClass()->getName();

                case 'static':
                    return ': ' . ($isNullable ? '?' : '') . $returnTypeName;

                case 'mixed':
                case 'void':
                case 'never':
                    // These types cannot be marked as nullable
                    return ': ' . $returnTypeName;

                    // PHP 8.2+: Standalone null, true, false types
                case 'null':
                case 'true':
                case 'false':
                    // These standalone types are returned as-is
                    return ': ' . $returnTypeName;
            }
        }

        // For complex types (Union, Intersection, DNF), use the generic formatter
        if ($returnType instanceof \ReflectionUnionType
            || (class_exists(\ReflectionIntersectionType::class) && $returnType instanceof \ReflectionIntersectionType)) {
            $formattedType = $this->formatReflectionType($returnType, $method);
            return $formattedType !== '' ? ': ' . $formattedType : '';
        }

        // Fallback for simple types (including mocked types and tentative return types)
        // Handle special keywords
        switch ($returnTypeName) {
            case 'self':
                return ': ' . ($isNullable ? '?' : '') . '\\' . $method->getDeclaringClass()->getName();

            case 'parent':
                return ': ' . ($isNullable ? '?' : '') . '\\' . $method->getDeclaringClass()->getParentClass()->getName();

            case 'static':
            case 'mixed':
            case 'null':
                return ': ' . ($isNullable && !in_array($returnTypeName, ['mixed', 'null']) ? '?' : '') . $returnTypeName;
        }

        // Check if it's a builtin type (either directly or from mocked ReflectionType)
        $isBuiltinType = ($returnType instanceof \reflectionNamedType && $returnType->isBuiltin())
                      || ($returnType->isBuiltin());

        if ($isBuiltinType) {
            return ': ' . ($isNullable ? '?' : '') . $returnTypeName;
        }

        // For non-builtin types (classes), add backslash
        return ': ' . ($isNullable ? '?' : '') . '\\' . $returnTypeName;
    }

    protected function hasReturnType(\reflectionMethod $method): bool
    {
        $hasReturnType = $method->hasReturnType() === true;

        if (!$hasReturnType && version_compare(phpversion(), '8.1', '>=')) {
            $hasReturnType = $method->hasTentativeReturnType();
        }

        return $hasReturnType;
    }

    protected function getReflectionType(\reflectionMethod $method): ?\ReflectionType
    {
        if (!$this->hasReturnType($method)) {
            return null;
        }

        $returnType = $method->getReturnType();

        if ($returnType === null && version_compare(phpversion(), '8.1', '>=') && $method->hasTentativeReturnType()) {
            $returnType = $method->getTentativeReturnType();
        }

        return $returnType;
    }

    protected function getReflectionTypeName(\reflectionType $type): string
    {
        return $type instanceof \reflectionNamedType ? $type->getName() : (string) $type;
    }

    protected function isVoid(\reflectionMethod $method): bool
    {
        if (!$this->hasReturnType($method)) {
            return false;
        }
        
        $returnTypeName = $this->getReflectionTypeName($this->getReflectionType($method));
        return $returnTypeName === 'void' || $returnTypeName === 'never';
    }

    /**
     * Generate a default return value for a given return type.
     * Returns appropriate default values for non-nullable types to avoid type errors.
     */
    protected function getDefaultReturnValue(\ReflectionType $returnType, \reflectionMethod $method, \reflectionClass $class, bool $isStatic = false): string
    {
        $isNullable = $returnType->allowsNull();

        // If nullable, return null
        if ($isNullable) {
            return 'null';
        }

        // Handle union types - find first non-nullable type
        if ($returnType instanceof \ReflectionUnionType) {
            $types = $returnType->getTypes();
            foreach ($types as $type) {
                if ($type->allowsNull() === false) {
                    return $this->getDefaultReturnValueForType($type, $method, $class, $isStatic);
                }
            }
            // If all types are nullable, return null
            return 'null';
        }

        // Handle intersection types
        if (class_exists(\ReflectionIntersectionType::class) && $returnType instanceof \ReflectionIntersectionType) {
            $types = $returnType->getTypes();
            if (count($types) > 0) {
                // For intersection types, use the first type
                return $this->getDefaultReturnValueForType($types[0], $method, $class, $isStatic);
            }
        }

        return $this->getDefaultReturnValueForType($returnType, $method, $class, $isStatic);
    }

    /**
     * Get default return value for a specific ReflectionNamedType.
     */
    protected function getDefaultReturnValueForType(\ReflectionType $type, \reflectionMethod $method, \reflectionClass $class, bool $isStatic = false): string
    {
        if (!($type instanceof \ReflectionNamedType)) {
            return 'null';
        }

        $typeName = $type->getName();

        // Handle special cases: self, static, parent
        switch ($typeName) {
            case 'self':
                return $isStatic ? 'new self()' : '$this';
            case 'static':
                return $isStatic ? 'new static()' : '$this';
            case 'parent':
                if ($isStatic) {
                    return $class->getParentClass() ? 'new parent()' : 'new static()';
                }
                return '$this';
        }

        // Handle built-in types
        if ($type->isBuiltin()) {
            switch ($typeName) {
                case 'array':
                    return '[]';
                case 'int':
                    return '0';
                case 'float':
                    return '0.0';
                case 'string':
                    return "''";
                case 'bool':
                    return 'false';
                case 'callable':
                    return 'function() {}';
                case 'iterable':
                    return '[]';
                case 'object':
                    return 'new \stdClass()';
                case 'mixed':
                    return 'null';
                case 'void':
                case 'never':
                    return '';
                case 'null':
                    return 'null';
                case 'true':
                    return 'true';
                case 'false':
                    return 'false';
                default:
                    return 'null';
            }
        }

        // For class/interface types
        try {
            $reflectionClass = new \ReflectionClass($typeName);
            if ($reflectionClass->isInstantiable()) {
                $constructor = $reflectionClass->getConstructor();
                if ($constructor === null || $constructor->getNumberOfRequiredParameters() === 0) {
                    return 'new \\' . $typeName . '()';
                }
            }
        } catch (\ReflectionException $e) {
            // Ignore
        }

        return 'null';
    }

    protected static function isDefaultParameterNull(\ReflectionParameter $parameter)
    {
        return $parameter->allowsNull() &&
               $parameter->isDefaultValueAvailable() &&
               null === $parameter->getDefaultValue();
    }

    protected function getParameters(\reflectionMethod $method): array
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

    protected function getParametersSignature(\reflectionMethod $method, bool $forceMockController = false): string
    {
        $parameters = [];

        $mustBeNull = $this->isOrphanized($method->getName());

        foreach ($method->getParameters() as $parameter) {
            $typeHintString = $this->parameterAnalyzer->getTypeHintString($parameter, $mustBeNull);
            $parameterCode = (!empty($typeHintString) ? $typeHintString . ' ' : '') . ($parameter->isPassedByReference() == false ? '' : '& ') . ($parameter->isVariadic() == false ? '' : '... ') . '$' . $parameter->getName();

            switch (true) {
                case $parameter->isDefaultValueAvailable():
                    $parameterCode .= ' = ' . var_export($parameter->getDefaultValue(), true);
                    break;

                case self::isDefaultParameterNull($parameter):
                case $parameter->isOptional() && $parameter->isVariadic() == false:
                case $mustBeNull && $parameter->isVariadic() == false:
                    $parameterCode .= ' = null';
            }

            $parameters[] = $parameterCode;
        }

        if (self::hasVariadic($method) === false && ($method->isConstructor() || $forceMockController)) {
            $parameters[] = '?\\' . __NAMESPACE__ . '\\controller $mockController = null';
        }

        return implode(', ', $parameters);
    }

    protected function canCallParent(): bool
    {
        return $this->shuntParentClassCalls === false && $this->allIsInterface === false;
    }

    protected static function getClassName($class)
    {
        $class = ltrim($class, '\\');
        $lastAntiSlash = strrpos($class, '\\');

        return ($lastAntiSlash === false ? $class : substr($class, $lastAntiSlash + 1));
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
            "\t" . 'public function getMockController(): \atoum\atoum\mock\controller' . PHP_EOL .
            "\t" . '{' . PHP_EOL .
            "\t\t" . '$mockController = \atoum\atoum\mock\controller::getForMock($this);' . PHP_EOL .
            "\t\t" . 'if ($mockController === null)' . PHP_EOL .
            "\t\t" . '{' . PHP_EOL .
            "\t\t\t" . '$this->setMockController($mockController = new \\' . __NAMESPACE__ . '\\controller());' . PHP_EOL .
            "\t\t" . '}' . PHP_EOL .
            "\t\t" . 'return $mockController;' . PHP_EOL .
            "\t" . '}' . PHP_EOL .
            "\t" . 'public function setMockController(\\' . __NAMESPACE__ . '\\controller $controller): static' . PHP_EOL .
            "\t" . '{' . PHP_EOL .
            "\t\t" . '$controller->control($this);' . PHP_EOL .
            "\t\t" . 'return $this;' . PHP_EOL .
            "\t" . '}' . PHP_EOL .
            "\t" . 'public function resetMockController(): static' . PHP_EOL .
            "\t" . '{' . PHP_EOL .
            "\t\t" . '\atoum\atoum\mock\controller::getForMock($this)->reset();' . PHP_EOL .
            "\t\t" . 'return $this;' . PHP_EOL .
            "\t" . '}' . PHP_EOL
        ;
    }

    protected static function generateDefaultConstructor($disableMethodChecking = false, $uniqueId = false)
    {
        $defaultConstructor =
            "\t" . 'public function __construct(?\\' . __NAMESPACE__ . '\\controller $mockController = null)' . PHP_EOL .
            "\t" . '{' . PHP_EOL;

        if ($uniqueId === true) {
            $defaultConstructor .= self::generateUniqueId();
        }

        $defaultConstructor .=
            "\t\t" . 'if ($mockController === null)' . PHP_EOL .
            "\t\t" . '{' . PHP_EOL .
            "\t\t\t" . '$mockController = \atoum\atoum\mock\controller::get();' . PHP_EOL .
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
            "\t" . 'public static function getMockedMethods(): array' . PHP_EOL .
            "\t" . '{' . PHP_EOL .
            "\t\t" . 'return ' . var_export($mockedMethodNames, true) . ';' . PHP_EOL .
            "\t" . '}' . PHP_EOL
        ;
    }

    protected static function generateUnknownClassCode($mockNamespace, $mockClass, $uniqueId = false, $useStrictTypes = false)
    {
        return ($useStrictTypes ? 'declare(strict_types=1);' . PHP_EOL : '') .
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

    protected static function methodNameIsReservedWord(\reflectionMethod $method): bool
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

    /**
     * Generate promoted properties from constructor (PHP 8.0+)
     */
    protected function generatePromotedProperties(\ReflectionClass $class): string
    {
        $propertiesCode = '';

        $constructor = $class->getConstructor();
        if ($constructor === null) {
            return '';
        }

        // In PHP 8.2+, if the class is readonly, all properties are implicitly readonly
        // and inherited. We should NOT redeclare them to avoid "Cannot redeclare readonly property" error
        $isReadonlyClass = version_compare(PHP_VERSION, '8.2.0', '>=') && $class->isReadOnly();
        if ($isReadonlyClass) {
            return '';
        }

        foreach ($constructor->getParameters() as $parameter) {
            if (version_compare(PHP_VERSION, '8.0.0', '>=') && $parameter->isPromoted()) {
                // In PHP 8.4+, readonly promoted properties automatically get asymmetric visibility
                // and will be handled by generatePropertiesWithAsymmetricVisibility() instead
                // Redeclaring them here would cause "Cannot redeclare readonly property" error
                if (version_compare(PHP_VERSION, '8.4.0', '>=')) {
                    $reflector = $parameter->getDeclaringFunction()->getDeclaringClass();
                    $property = $reflector->getProperty($parameter->getName());

                    if ($property->isReadOnly()) {
                        // Skip readonly promoted properties in PHP 8.4+ (handled elsewhere)
                        continue;
                    }
                }

                $propertiesCode .= $this->generatePromotedProperty($parameter);
            }
        }

        return $propertiesCode;
    }

    /**
     * Generate code for a single promoted property
     */
    protected function generatePromotedProperty(\ReflectionParameter $parameter): string
    {
        $propertyName = $parameter->getName();

        // Get the property from the class to determine visibility
        $reflector = $parameter->getDeclaringFunction()->getDeclaringClass();
        $property = $reflector->getProperty($propertyName);

        $visibility = 'public';
        if ($property->isProtected()) {
            $visibility = 'protected';
        } elseif ($property->isPrivate()) {
            $visibility = 'private';
        }

        // Check if readonly (PHP 8.1+)
        $readonly = '';
        if (version_compare(PHP_VERSION, '8.1.0', '>=') && $property->isReadOnly()) {
            $readonly = 'readonly ';
        }

        // Get property type
        $typeHint = '';
        if ($property->hasType()) {
            $typeHint = $this->getPropertyType($property);
            if ($typeHint !== '') {
                $typeHint .= ' ';
            }
        }

        return "\t" . $visibility . ' ' . $readonly . $typeHint . '$' . $propertyName . ';' . PHP_EOL;
    }

    /**
     * Check if a property has hooks (PHP 8.4+)
     * Note: Caller must ensure PHP version >= 8.4
     */
    protected function hasPropertyHooks(\ReflectionProperty $property): bool
    {
        $hooks = $property->getHooks();
        return !empty($hooks);
    }

    /**
     * Generate code for properties with hooks
     */
    protected function generatePropertiesWithHooks(\ReflectionClass $class): string
    {
        if (version_compare(PHP_VERSION, '8.4.0', '<')) {
            return '';
        }

        $propertiesCode = '';

        try {
            foreach ($class->getProperties() as $property) {
                if ($this->hasPropertyHooks($property)) {
                    $propertiesCode .= $this->generatePropertyWithHook($class, $property);
                }
            }
        } catch (\Throwable $e) {
            // Ignore errors when getting properties (e.g., mocked ReflectionClass on PHP 8.4+)
        }

        return $propertiesCode;
    }

    /**
     * Generate code for a single property with hooks
     */
    protected function generatePropertyWithHook(\ReflectionClass $class, \ReflectionProperty $property): string
    {
        $propertyName = $property->getName();
        $visibility = $this->getPropertyVisibility($property);

        // Get property type if available
        $typeHint = $this->getPropertyType($property);
        if ($typeHint !== '') {
            $typeHint .= ' ';
        }

        $code = "\t" . $visibility . ' ' . $typeHint . '$' . $propertyName . ' {' . PHP_EOL;

        // Generate hooks
        $hooks = $property->getHooks();

        // Note: hooks array uses string keys 'get' and 'set', not constants
        if (isset($hooks['get'])) {
            $code .= "\t\t" . 'get {' . PHP_EOL;
            $code .= "\t\t\t" . 'return $this->getMockController()->invoke(\'__get_' . $propertyName . '\', []);' . PHP_EOL;
            $code .= "\t\t" . '}' . PHP_EOL;
        }

        if (isset($hooks['set'])) {
            // For set hook, parameter type must match property type
            $code .= "\t\t" . 'set(' . $typeHint . '$value) {' . PHP_EOL;
            $code .= "\t\t\t" . '$this->getMockController()->invoke(\'__set_' . $propertyName . '\', [$value]);' . PHP_EOL;
            $code .= "\t\t" . '}' . PHP_EOL;
        }

        $code .= "\t" . '}' . PHP_EOL . PHP_EOL;

        return $code;
    }

    /**
     * Get property type as string for code generation
     * Supports: Named types, Union types, Intersection types (PHP 8.1+), DNF types (PHP 8.2+)
     */
    protected function getPropertyType(\ReflectionProperty $property): string
    {
        if (!$property->hasType()) {
            return '';
        }

        $type = $property->getType();

        return $this->formatReflectionType($type);
    }

    /**
     * Format a ReflectionType into a string representation
     * Handles: NamedType, UnionType, IntersectionType (PHP 8.1+), and DNF types (PHP 8.2+)
     */
    protected function formatReflectionType(\ReflectionType $type, ?\reflectionMethod $method = null): string
    {
        // PHP 8.0+: Named types
        if ($type instanceof \ReflectionNamedType) {
            $typeName = $type->getName();

            // Handle special keywords: self, parent, static
            if ($method !== null) {
                $declaringClass = $method->getDeclaringClass();

                if ($typeName === 'self') {
                    $typeName = $declaringClass->getName();
                } elseif ($typeName === 'parent') {
                    $parentClass = $declaringClass->getParentClass();
                    $typeName = $parentClass->getName();
                } elseif ($typeName === 'static') {
                    // 'static' is kept as-is (late static binding)
                    $nullable = $type->allowsNull() && $typeName !== 'mixed' && $typeName !== 'null' ? '?' : '';
                    return $nullable . $typeName;
                }
            }

            $nullable = $type->allowsNull() && $typeName !== 'mixed' && $typeName !== 'null' ? '?' : '';

            // Handle special keyword types: self, parent, static
            // These must NEVER have a backslash prefix
            if (in_array($typeName, ['self', 'parent', 'static'])) {
                return $nullable . $typeName;
            }

            return $nullable . (!$type->isBuiltin() ? '\\' : '') . $typeName;
        }

        // PHP 8.0+: Union types
        if ($type instanceof \ReflectionUnionType) {
            $types = array_map(
                function ($t) use ($method) {
                    return $this->formatReflectionType($t, $method);
                },
                $type->getTypes()
            );
            return implode('|', $types);
        }

        // PHP 8.1+: Intersection types
        if (class_exists(\ReflectionIntersectionType::class) && $type instanceof \ReflectionIntersectionType) {
            $types = array_map(
                function ($t) use ($method) {
                    // For intersection types within DNF, we may need parentheses
                    $formatted = $this->formatReflectionType($t, $method);
                    // If the formatted type contains a union (|), wrap in parentheses
                    if (strpos($formatted, '|') !== false) {
                        return '(' . $formatted . ')';
                    }
                    return $formatted;
                },
                $type->getTypes()
            );
            return implode('&', $types);
        }

        return '';
    }

    /**
     * Check if a property has asymmetric visibility (PHP 8.4+)
     * Note: Caller must ensure PHP version >= 8.4
     */
    protected function hasAsymmetricVisibility(\ReflectionProperty $property): bool
    {
        // Get read visibility
        $isPublicRead = $property->isPublic();
        $isProtectedRead = $property->isProtected();
        $isPrivateRead = $property->isPrivate();

        // Get write visibility
        $isProtectedWrite = $property->isProtectedSet();
        $isPrivateWrite = $property->isPrivateSet();

        // If read and write visibilities differ, it's asymmetric
        if ($isPublicRead && ($isProtectedWrite || $isPrivateWrite)) {
            return true;
        }
        if ($isProtectedRead && $isPrivateWrite) {
            return true;
        }
        // Private read can't have more restrictive write visibility

        return false;
    }

    /**
     * Get property visibility declaration including asymmetric visibility (PHP 8.4+)
     */
    protected function getPropertyVisibility(\ReflectionProperty $property): string
    {
        $readVisibility = $property->isPublic() ? 'public' :
                         ($property->isProtected() ? 'protected' : 'private');

        // Check for asymmetric visibility (PHP 8.4+)
        if ($this->hasAsymmetricVisibility($property)) {
            $writeVisibility = $property->isProtectedSet() ? 'protected' : 'private';

            return $readVisibility . ' ' . $writeVisibility . '(set)';
        }

        return $readVisibility;
    }

    /**
     * Generate code for properties with asymmetric visibility
     */
    protected function generatePropertiesWithAsymmetricVisibility(\ReflectionClass $class): string
    {
        if (version_compare(PHP_VERSION, '8.4.0', '<')) {
            return '';
        }

        $propertiesCode = '';

        try {
            foreach ($class->getProperties() as $property) {
                if ($this->hasAsymmetricVisibility($property) && !$this->hasPropertyHooks($property)) {
                    $propertiesCode .= $this->generatePropertyWithAsymmetricVisibility($property);
                }
            }
        } catch (\Throwable $e) {
            // Ignore errors when getting properties (e.g., mocked ReflectionClass on PHP 8.4+)
        }

        return $propertiesCode;
    }

    /**
     * Generate code for a single property with asymmetric visibility
     */
    protected function generatePropertyWithAsymmetricVisibility(\ReflectionProperty $property): string
    {
        $propertyName = $property->getName();
        $visibility = $this->getPropertyVisibility($property);

        // Check if readonly (PHP 8.1+)
        $readonly = '';
        if (version_compare(PHP_VERSION, '8.1.0', '>=') && $property->isReadOnly()) {
            $readonly = 'readonly ';
        }

        // Get property type if available
        $typeHint = $this->getPropertyType($property);
        if ($typeHint !== '') {
            $typeHint .= ' ';
        }

        // For mocked properties with asymmetric visibility, we need to maintain the same visibility
        $code = "\t" . $visibility . ' ' . $readonly . $typeHint . '$' . $propertyName . ';' . PHP_EOL;

        return $code;
    }
}
