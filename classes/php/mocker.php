<?php

namespace atoum\atoum\php;

use atoum\atoum;

abstract class mocker
{
    protected string $defaultNamespace = '';
    protected ?\Closure $reflectedFunctionFactory = null;

    protected static ?atoum\test\adapter $adapter = null;
    protected static ?atoum\tools\parameter\analyzer $parameterAnalyzer = null;

    public function __construct(string $defaultNamespace = '')
    {
        $this->setDefaultNamespace($defaultNamespace);
    }

    abstract public function __get(string $name): mixed;

    abstract public function __set(string $name, mixed $mixed): void;

    abstract public function __isset(string $name): bool;

    abstract public function __unset(string $name): void;

    abstract public function addToTest(atoum\test $test): static;

    public function setDefaultNamespace(string $namespace): static
    {
        $this->defaultNamespace = trim($namespace, '\\');

        if ($this->defaultNamespace !== '') {
            $this->defaultNamespace .= '\\';
        }

        return $this;
    }

    public function getDefaultNamespace(): string
    {
        return $this->defaultNamespace;
    }

    public static function setAdapter(?atoum\test\adapter $adapter = null): void
    {
        static::$adapter = $adapter ?: new atoum\php\mocker\adapter();
    }

    public static function getAdapter(): ?atoum\test\adapter
    {
        return static::$adapter;
    }

    public static function setParameterAnalyzer(?atoum\tools\parameter\analyzer $analyzer = null): void
    {
        static::$parameterAnalyzer = $analyzer ?: new atoum\tools\parameter\analyzer();
    }

    public static function getParameterAnalyzer(): ?atoum\tools\parameter\analyzer
    {
        return static::$parameterAnalyzer;
    }
}

mocker::setAdapter();
mocker::setParameterAnalyzer();
