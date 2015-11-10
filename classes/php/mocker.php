<?php

namespace mageekguy\atoum\php;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

abstract class mocker
{
	protected $defaultNamespace = '';
	protected $reflectedFunctionFactory = null;

	protected static $adapter = null;

	public function __construct($defaultNamespace = '')
	{
		$this->setDefaultNamespace($defaultNamespace);
	}

	abstract public function __get($name);

	abstract public function __set($name, $mixed);

	abstract public function __isset($name);

	abstract public function __unset($name);

	abstract function addToTest(atoum\test $test);

	public function setDefaultNamespace($namespace)
	{
		$this->defaultNamespace = trim($namespace, '\\');

		if ($this->defaultNamespace !== '')
		{
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
}

mocker::setAdapter();
