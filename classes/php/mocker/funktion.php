<?php

namespace atoum\atoum\php\mocker;

use atoum\atoum;
use atoum\atoum\php\mocker;
use atoum\atoum\test\exceptions;

class funktion extends mocker
{
    public function __construct(string $defaultNamespace = '')
    {
        parent::__construct($defaultNamespace);

        $this->setReflectedFunctionFactory();
    }

    public function __get(string $functionName): mixed
    {
        return $this->getAdapter()->{$this->generateIfNotExists($functionName)};
    }

    public function __set(string $functionName, mixed $mixed): void
    {
        $this->getAdapter()->{$this->generateIfNotExists($functionName)} = $mixed;
    }

    public function __isset(string $functionName): bool
    {
        return $this->functionExists($this->getFqdn($functionName));
    }

    public function __unset(string $functionName): void
    {
        $this->setDefaultBehavior($this->getFqdn($functionName));
    }

    public function setReflectedFunctionFactory(?\Closure $factory = null): static
    {
        $this->reflectedFunctionFactory = $factory ?: function ($functionName) {
            return new \reflectionFunction($functionName);
        };

        return $this;
    }

    public function useClassNamespace(string $className): static
    {
        return $this->setDefaultNamespace(substr($className, 0, strrpos($className, '\\')));
    }

    public function generate(string $functionName): static
    {
        $fqdn = $this->getFqdn($functionName);

        if ($this->functionExists($fqdn) === false) {
            $lastAntislash = strrpos($fqdn, '\\');
            $namespace = substr($fqdn, 0, $lastAntislash);
            $function = substr($fqdn, $lastAntislash > 0 ? $lastAntislash + 1 : 0);

            if (function_exists($fqdn) === true) {
                $message = $namespace === '' ? 'This may be because you are trying to mock a function from a class in the root namespace.' : 'This may be because a function with the same name already exists in the namespace \'' . $namespace . '\'.';

                throw new exceptions\runtime('The function you are trying to mock already exists: \'' . $function . '\'. ' . $message);
            }

            $reflectedFunction = $this->buildReflectedFunction($function);
            static::defineMockedFunction($namespace, get_class($this), $function, $reflectedFunction);
        }

        return $this->setDefaultBehavior($fqdn);
    }

    public function resetCalls(?string $functionName = null): static
    {
        static::$adapter->resetCalls($this->getFqdn($functionName));

        return $this;
    }

    public function addToTest(atoum\test $test): static
    {
        $test->setPhpFunctionMocker($this);

        return $this;
    }

    protected function getFqdn(?string $functionName): string
    {
        if ($functionName === null) {
            return $this->defaultNamespace;
        }

        return $this->defaultNamespace . $functionName;
    }

    protected function generateIfNotExists(string $functionName): string
    {
        if (isset($this->{$functionName}) === false) {
            $this->generate($functionName);
        }

        return $this->getFqdn($functionName);
    }

    protected function setDefaultBehavior($fqdn, ?\reflectionFunction $reflectedFunction = null)
    {
        $function = substr($fqdn, strrpos($fqdn, '\\') + 1);

        if ($reflectedFunction === null) {
            $reflectedFunction = $this->buildReflectedFunction($function);
        }

        if ($reflectedFunction === null) {
            $closure = function () {
                return null;
            };
        } else {
            $closure = eval('return function(' . static::getParametersSignature($reflectedFunction) . ') { return call_user_func_array(\'\\' . $function . '\', ' . static::getParameters($reflectedFunction) . '); };');
        }

        static::$adapter->{$fqdn}->setClosure($closure);

        return $this;
    }

    protected function functionExists($fqdn)
    {
        return (isset(static::$adapter->{$fqdn}) === true);
    }

    protected static function getParametersSignature(\reflectionFunction $function)
    {
        $parameters = [];

        foreach (self::filterParameters($function) as $parameter) {
            $typeHintString = static::$parameterAnalyzer->getTypeHintString($parameter);
            $parameterCode = (!empty($typeHintString) ? $typeHintString . ' ' : '') . ($parameter->isPassedByReference() == false ? '' : '& ') . '$' . $parameter->getName();

            switch (true) {
                case $parameter->isDefaultValueAvailable():
                    $parameterCode .= ' = ' . var_export($parameter->getDefaultValue(), true);
                    break;
                case $parameter->isOptional():
                    $parameterCode .= ' = null';
            }

            $parameters[] = $parameterCode;
        }

        return implode(', ', $parameters);
    }

    protected static function getParameters(\reflectionFunction $function)
    {
        $parameters = [];

        foreach (self::filterParameters($function) as $parameter) {
            $parameters[] = ($parameter->isPassedByReference() === false ? '' : '& ') . '$' . $parameter->getName();
        }

        return 'array(' . implode(',', $parameters) . ')';
    }

    protected static function defineMockedFunction($namespace, $class, $function, ?\reflectionFunction $reflectedFunction = null)
    {
        eval(sprintf(
            'namespace %s { function %s(%s) { return \\%s::getAdapter()->invoke(__FUNCTION__, %s); } }',
            $namespace,
            $function,
            $reflectedFunction ? static::getParametersSignature($reflectedFunction) : '',
            $class,
            $reflectedFunction ? static::getParameters($reflectedFunction) : 'func_get_args()'
        ));
    }

    private function buildReflectedFunction($function)
    {
        $reflectedFunction = null;

        try {
            $reflectedFunction = call_user_func_array($this->reflectedFunctionFactory, [$function]);
        } catch (\exception $exception) {
        }

        return $reflectedFunction;
    }

    private static function filterParameters(\reflectionFunction $function)
    {
        return array_filter($function->getParameters(), function ($parameter) {
            return ($parameter->getName() != '...');
        });
    }
}
