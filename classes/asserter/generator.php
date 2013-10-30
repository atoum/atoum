<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class generator
{
	const defaultAsserterNamespace = 'mageekguy\atoum\asserters';

	protected $aliases = array();
	protected $locale = null;
	protected $adapter = null;
	protected $asserterNamespace = '';

	public function __construct(atoum\locale $locale = null, atoum\adapter $adapter = null, $asserterNamespace = null)
	{
		$this
			->setAdapter($adapter)
			->setLocale($locale)
			->setAsserterNamespace($asserterNamespace)
		;
	}

	public function __set($asserter, $class)
	{
		$this->setAlias($asserter, $class);
	}

	public function __get($property)
	{
		return $this->getAsserterInstance($property);
	}

	public function __isset($property)
	{
		return ($this->getAsserterClass($property) !== null);
	}

	public function __call($method, $arguments)
	{
		return $this->getAsserterInstance($method, $arguments);
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

	public function setLocale(atoum\locale $locale = null)
	{
		$this->locale = $locale ?: new atoum\locale();

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setAsserterNamespace($namespace = null)
	{
		$this->asserterNamespace = ($namespace === null ? static::defaultAsserterNamespace : trim($namespace, '\\')) . '\\';

		return $this;
	}

	public function getAsserterNamespace()
	{
		return trim($this->asserterNamespace, '\\');
	}

	public function setAlias($alias, $asserterClass)
	{
		$this->aliases[strtolower($alias)] = $asserterClass;

		return $this;
	}

	public function getAliases()
	{
		return $this->aliases;
	}

	public function resetAliases()
	{
		$this->aliases = array();

		return $this;
	}

	public function asserterPass(atoum\asserter $asserter)
	{
		return $this;
	}

	public function asserterFail(atoum\asserter $asserter, $reason)
	{
		throw new exception($reason);
	}

	public function getAsserterClass($asserter)
	{
		$asserter = strtolower($asserter);

		$class = (isset($this->aliases[$asserter]) === false ? $asserter : $this->aliases[$asserter]);

		if (substr($class, 0, 1) != '\\')
		{
			$class = $this->asserterNamespace . $class;
		}

		if ($this->adapter->class_exists($class, true) === false)
		{
			$class = null;
		}

		return $class;
	}

	public function getAsserterInstance($asserter, array $arguments = array())
	{
		if (($asserterClass = $this->getAsserterClass($asserter)) === null)
		{
			throw new exceptions\logic\invalidArgument('Asserter \'' . $asserter . '\' does not exist');
		}

		$asserterInstance = new $asserterClass();

		return $asserterInstance
			->setGenerator($this)
			->setWithArguments($arguments)
		;
	}
}
