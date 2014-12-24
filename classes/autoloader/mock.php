<?php

namespace mageekguy\atoum\autoloader;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class mock
{
	protected $mockGenerator;
	protected $adapter;

	public function __construct(atoum\mock\generator $generator = null, atoum\adapter $adapter = null)
	{
		$this
			->setAdapter($adapter)
			->setMockGenerator($generator)
		;
	}

	public function setMockGenerator(atoum\mock\generator $generator = null)
	{
		$this->mockGenerator = $generator ?: new atoum\mock\generator();

		return $this;
	}

	public function getMockGenerator()
	{
		return $this->mockGenerator;
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

	public function register()
	{
		if ($this->adapter->spl_autoload_register(array($this, 'requireClass'), true, true) === false)
		{
			throw new exceptions\runtime('Unable to register mock autoloader');
		}

		return $this;
	}

	public function unregister()
	{
		if ($this->adapter->spl_autoload_unregister(array($this, 'requireClass')) === false)
		{
			throw new exceptions\runtime('Unable to unregister mock autoloader');
		}

		return $this;
	}

	public function requireClass($class)
	{
		$mockNamespace = ltrim($this->mockGenerator->getDefaultNamespace(), '\\');
		$mockNamespacePattern = '/^\\\?' . preg_quote($mockNamespace) . '\\\/i';
		$mockedClass = preg_replace($mockNamespacePattern, '', $class);

		if ($mockedClass !== $class)
		{
			$this->mockGenerator->generate($mockedClass);
		}

		return $this;
	}
} 
