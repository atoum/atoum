<?php

namespace atoum\atoum\php;

use atoum\atoum;

abstract class mocker
{
    protected $defaultNamespace = '';
    protected $reflectedFunctionFactory = null;

    protected static $adapter = null;
    protected static $parameterAnalyzer = null;

    public function __construct($defaultNamespace = '')
    {
        $this->setDefaultNamespace($defaultNamespace);
    }

    abstract public function __get($name);

    abstract public function __set($name, $mixed);

    abstract public function __isset($name);

    abstract public function __unset($name);

    abstract public function addToTest(atoum\test $test);

    public function setDefaultNamespace($namespace)
    {
        $this->defaultNamespace = trim($namespace, '\\');

        if ($this->defaultNamespace !== '') {
            $this->defaultNamespace .= '\\';
        }

        return $this;
    }

    public function getDefaultNamespace()
    {
        return $this->defaultNamespace;
    }

    public static function setAdapter(atoum\test\adapter $adapter = null)
    {
        static::$adapter = $adapter ?: new atoum\php\mocker\adapter();
    }

    public static function getAdapter()
    {
        return static::$adapter;
    }

    public static function setParameterAnalyzer(atoum\tools\parameter\analyzer $analyzer = null)
    {
        static::$parameterAnalyzer = $analyzer ?: new atoum\tools\parameter\analyzer();
    }

    public static function getParameterAnalyzer()
    {
        return static::$parameterAnalyzer;
    }
}

mocker::setAdapter();
mocker::setParameterAnalyzer();
