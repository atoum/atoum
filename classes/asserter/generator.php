<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

class generator
{
	const defaultAsserterNamespace = 'mageekguy\atoum\asserters';

	protected $aliases = array();
	protected $locale = null;
	protected $resolver = null;
	protected $asserterNamespace = '';

	public function __construct(atoum\locale $locale = null, atoum\adapter $adapter = null, $asserterNamespace = null)
	{
		$this
			->setLocale($locale)
			->setResolver()
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

	public function setLocale(atoum\locale $locale = null)
	{
		$this->locale = $locale ?: new atoum\locale();

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setResolver(asserter\resolver $resolver = null)
	{
		$this->resolver = $resolver ?: new asserter\resolver();

		return $this;
	}

	public function getResolver()
	{
		return $this->resolver;
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

	public function getAsserterClass($asserter)
	{
		return $this->resolver->resolve($this->resolveAlias($asserter));
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

	private function resolveAlias($alias)
	{
		$alias = strtolower($alias);

		return (isset($this->aliases[$alias]) === false ? $alias : $this->aliases[$alias]);
	}
}
