<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\assertion
;

class generator
{
	protected $locale = null;
	protected $resolver = null;
	protected $aliaser = null;

	public function __construct(atoum\locale $locale = null, asserter\resolver $resolver = null, assertion\aliaser $aliaser = null)
	{
		$this
			->setLocale($locale)
			->setResolver($resolver)
			->setAliaser($aliaser)
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

	public function setBaseClass($baseClass)
	{
		$this->resolver->setBaseClass($baseClass);

		return $this;
	}

	public function getBaseClass()
	{
		return $this->resolver->getBaseClass();
	}

	public function addNamespace($namespace)
	{
		$this->resolver->addNamespace($namespace);

		return $this;
	}

	public function getNamespaces()
	{
		return $this->resolver->getNamespaces();
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

	public function setAliaser(assertion\aliaser $aliaser = null)
	{
		$this->aliaser = $aliaser ?: new assertion\aliaser();

		return $this;
	}

	public function getAliaser()
	{
		return $this->aliaser;
	}

	public function setAlias($alias, $asserterClass)
	{
		$this->aliaser->aliasClass($asserterClass, $alias);

		return $this;
	}

	public function getAliases()
	{
		return $this->aliaser->getClassAliases();
	}

	public function resetAliases()
	{
		$this->aliaser->resetClassAliases();

		return $this;
	}

	public function getAsserterClass($asserter)
	{
		return $this->resolver->resolve($this->aliaser->resolveClass($asserter));
	}

	public function getAsserterInstance($asserter, array $arguments = array(), atoum\test $test = null)
	{
		if (($asserterClass = $this->getAsserterClass($asserter)) === null)
		{
			throw new exceptions\logic\invalidArgument('Asserter \'' . $asserter . '\' does not exist');
		}

		$asserterInstance = new $asserterClass();

		if ($test !== null)
		{
			$asserterInstance->setWithTest($test);
		}

		return $asserterInstance
			->setGenerator($this)
			->setLocale($this->locale)
			->setWithArguments($arguments)
		;
	}
}
