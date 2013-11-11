<?php

namespace mageekguy\atoum\test\phpunit\mock;

use mageekguy\atoum;

class builder
{
	protected $test;
	protected $className;
	protected $methods = array();
	protected $mockClassName = '';
	protected $constructorArgs = array();
	protected $originalConstructor = true;
	protected $originalClone = true;
	protected $autoload = true;
	protected $cloneArguments = false;

	public function __construct(atoum\test\phpunit\test $test, $className)
	{
		$this->test  = $test;
		$this->className = $className;
	}

	public function getMock()
	{
		$reflectionFactory = $this->test->getMockGenerator()->getReflectionClassFactory();

		if($this->originalConstructor === false) {
			$this->test->getMockGenerator()->orphanize('__construct');
		}

		$classname = '\\' . ltrim($this->test->getMockGenerator()->getDefaultnamespace(), '\\') . '\\' . trim($this->mockClassName ?: $this->className ,'\\');
		if (class_exists($classname, $this->autoload) === false)
		{
			$this->test->getMockGenerator()->generate($this->className, null, $this->mockClassName);
		}

		$mock = null;
		if(sizeof($this->constructorArgs) > 0) {
			$mock = $reflectionFactory($classname)->newInstanceArgs($this->constructorArgs);
		}

		$mock = $mock ?: new $classname();

		if ($this->methods === array())
		{
			foreach ($reflectionFactory($this->className)->getMethods() as $method)
			{
				if ($method->isPublic() && $method->isStatic() === false)
				{
					$mock->getMockController()->{$method->getName()} = null;
				}
			}
		}
		else
		{
			foreach ($this->methods as $method)
			{
				$mock->getMockController()->{$method} = null;
			}
		}

		return $mock;
	}

	public function getMockForAbstractClass()
	{
		return $this->testCase->getMockForAbstractClass(
			$this->className,
			$this->constructorArgs,
			$this->mockClassName,
			$this->originalConstructor,
			$this->originalClone,
			$this->autoload,
			$this->methods,
			$this->cloneArguments
		);
	}

	public function setMethods($methods)
	{
		$this->methods = $methods;

		return $this;
	}

	public function setConstructorArgs(array $args)
	{
		$this->constructorArgs = $args;

		return $this;
	}

	public function setMockClassName($name)
	{
		$this->mockClassName = $name;

		return $this;
	}

	public function disableOriginalConstructor($disable = true)
	{
		$this->originalConstructor = !$disable;

		return $this;
	}

	public function enableOriginalConstructor($enable = true)
	{
		$this->originalConstructor = $enable;

		return $this;
	}

	public function disableOriginalClone($disable = true)
	{
		$this->originalClone = !$disable;

		return $this;
	}

	public function enableOriginalClone($enable = true)
	{
		$this->originalClone = $enable;

		return $this;
	}

	public function disableAutoload($disable = true)
	{
		$this->autoload = !$disable;

		return $this;
	}

	public function enableAutoload($enable = true)
	{
		$this->autoload = $enable;

		return $this;
	}

	public function disableArgumentCloning($disable = true)
	{
		$this->cloneArguments = !$disable;

		return $this;
	}

	public function enableArgumentCloning($enable = true)
	{
		$this->cloneArguments = $enable;

		return $this;
	}
}