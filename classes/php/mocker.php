<?php

namespace mageekguy\atoum\php;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class mocker
{
	protected $defaultNamespace = '';
	protected $reflectedFunctionFactory = null;

	protected static $adapter = null;

	public function __construct($defaultNamespace = '')
	{
		$this
			->setDefaultNamespace($defaultNamespace)
			->setReflectedFunctionFactory()
		;
	}

	public function __get($functionName)
	{
		return static::$adapter->{$this->getFqdn($functionName)};
	}

	public function __set($functionName, $mixed)
	{
		if (isset($this->{$functionName}) === false)
		{
			$this->generate($functionName);
		}

		static::$adapter->{$this->getFqdn($functionName)} = $mixed;

		return $this;
	}

	public function __isset($functionName)
	{
		return (isset(static::$adapter->{$this->getFqdn($functionName)}) === true);
	}

	public function __unset($functionName)
	{
		$this->setDefaultBehavior($this->getFqdn($functionName));
	}

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

	public function setReflectedFunctionFactory(\closure $factory = null)
	{
		$this->reflectedFunctionFactory = $factory ?: function($functionName) { return new \reflectionFunction($functionName); };

		return $this;
	}

	public function useClassNamespace($className)
	{
		return $this->setDefaultNamespace(substr($className, 0, strrpos($className, '\\')));
	}

	public function generate($functionName)
	{
		$fqdn = $this->getFqdn($functionName);

		if (isset($this->{$functionName}) === true)
		{
			$this->setDefaultBehavior($fqdn);
		}
		else
		{
			if (function_exists($fqdn) === true)
			{
				throw new exceptions\logic\invalidArgument('Function \'' . $fqdn . '\' already exists');
			}

			$lastAntislash = strrpos($fqdn, '\\');
			$namespace = substr($fqdn, 0, $lastAntislash);
			$function = substr($fqdn, $lastAntislash + 1);

			try
			{
				$reflectedFunction = call_user_func_array($this->reflectedFunctionFactory, array($function));
			}
			catch (\exception $exception)
			{
				throw new exceptions\logic\invalidArgument('Function \'' . $fqdn . '\' does not exist');
			}

			$this->setDefaultBehavior($fqdn, $reflectedFunction);

			eval('namespace ' . $namespace . ' { function ' . $function . '(' . static::getParametersSignature($reflectedFunction) . ') { return \\' . get_class($this) . '::getAdapter()->invoke(__FUNCTION__, ' . static::getParameters($reflectedFunction) . '); } }');
		}

		return $this;
	}

	public static function setAdapter(atoum\test\adapter $adapter = null)
	{
		static::$adapter = $adapter ?: new atoum\test\adapter();
	}

	public static function getAdapter()
	{
		return static::$adapter;
	}

	protected function getFqdn($functionName)
	{
		return $this->defaultNamespace . $functionName;
	}

	protected function setDefaultBehavior($fqdn, \reflectionFunction $reflectedFunction = null)
	{
		$function = substr($fqdn, strrpos($fqdn, '\\') + 1);

		if ($reflectedFunction === null)
		{
			try
			{
				$reflectedFunction = call_user_func_array($this->reflectedFunctionFactory, array($function));
			}
			catch (\exception $exception)
			{
				throw new exceptions\logic\invalidArgument('Function \'' . $fqdn . '\' does not exist');
			}
		}

		static::$adapter->{$fqdn}->setClosure(eval('return function(' . static::getParametersSignature($reflectedFunction) . ') { return call_user_func_array(\'\\' . $function . '\', ' . static::getParameters($reflectedFunction) . '); };'));
	}

	protected static function getParametersSignature(\reflectionFunction $function)
	{
		$parameters = array();

		foreach ($function->getParameters() as $parameter)
		{
			$parameterCode = self::getParameterType($parameter) . ($parameter->isPassedByReference() == false ? '' : '& ') . '$' . $parameter->getName();

			switch (true)
			{
				case $parameter->isDefaultValueAvailable():
					$parameterCode .= ' = ' . var_export($parameter->getDefaultValue(), true);
					break;

				case $parameter->isOptional():
					$parameterCode .= ' = null';
			}

			$parameters[] = $parameterCode;
		}

		return join(', ', $parameters);
	}

	protected static function getParameters(\reflectionFunction $function)
	{
		$parameters = array();

		foreach ($function->getParameters() as $parameter)
		{
			$parameters[] = ($parameter->isPassedByReference() === false ? '' : '& ') . '$' . $parameter->getName();
		}

		return 'array(' . join(',', $parameters) . ')';
	}

	protected static function getParameterType(\reflectionParameter $parameter)
	{
		switch (true)
		{
			case $parameter->isArray():
				return 'array ';

			case method_exists($parameter, 'isCallable') && $parameter->isCallable():
				return 'callable ';

			case ($class = $parameter->getClass()):
				return '\\' . $class->getName() . ' ';

			default:
				return '';
		}
	}
}

mocker::setAdapter();
