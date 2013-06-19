<?php

namespace mageekguy\atoum\test\phpunit\mock;

use mageekguy\atoum;

class builder extends atoum\test\mock\generator
{
	protected $testCase;
	protected $className;
	protected $methods = array();
	protected $mockClassName = '';
	protected $constructorArgs = array();
	protected $originalConstructor = true;
	protected $originalClone = true;
	protected $autoload = true;
	protected $cloneArguments = false;

	public function __construct(atoum\test\phpunit\test $testCase, $className)
	{
		$this->testCase  = $testCase;
		$this->className = $className;
	}

	public function getMock()
	{
		return $this->testCase->getMock(
			$this->className,
			$this->methods,
			$this->constructorArgs,
			$this->mockClassName,
			$this->originalConstructor,
			$this->originalClone,
			$this->autoload,
			$this->cloneArguments
		);
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

	public function disableOriginalConstructor()
	{
		$this->originalConstructor = false;

		return $this;
	}

	public function enableOriginalConstructor()
	{
		$this->originalConstructor = true;

		return $this;
	}

	public function disableOriginalClone()
	{
		$this->originalClone = false;

		return $this;
	}

	public function enableOriginalClone()
	{
		$this->originalClone = true;

		return $this;
	}

	public function disableAutoload()
	{
		$this->autoload = false;

		return $this;
	}

	public function enableAutoload()
	{
		$this->autoload = true;

		return $this;
	}

	public function disableArgumentCloning()
	{
		$this->cloneArguments = false;

		return $this;
	}

	public function enableArgumentCloning()
	{
		$this->cloneArguments = true;

		return $this;
	}
}