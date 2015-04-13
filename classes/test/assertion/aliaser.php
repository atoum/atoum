<?php

namespace mageekguy\atoum\test\assertion;

use mageekguy\atoum\asserter;

class aliaser implements \arrayAccess
{
	protected $resolver = null;
	protected $aliases = array();

	private $context = null;
	private $keyword = null;

	public function __construct(asserter\resolver $resolver = null)
	{
		$this->setResolver($resolver);
	}

	public function __set($alias, $keyword)
	{
		return $this->aliasKeyword($keyword, $alias);
	}

	public function __get($alias)
	{
		return $this->resolveAlias($alias);
	}

	public function __unset($alias)
	{
		$contextKey = $this->getContextKey($this->context);

		if (isset($this->aliases[$contextKey]) === true)
		{
			$aliasKey = $this->getAliasKey($alias);

			if (isset($this->aliases[$contextKey][$aliasKey]) === true)
			{
				unset($this->aliases[$contextKey][$aliasKey]);
			}
		}
	}

	public function __isset($alias)
	{
		$contextKey = $this->getContextKey($this->context);

		if (isset($this->aliases[$contextKey]) === true)
		{
			$aliasKey = $this->getAliasKey($alias);

			return (isset($this->aliases[$contextKey][$aliasKey]) === true);
		}
	}

	public function offsetGet($context)
	{
		$this->context = $context;

		return $this;
	}

	public function offsetSet($newContext, $context)
	{
		$contextKey = $this->getContextKey($context);

		if (isset($this->aliases[$contextKey]) === true)
		{
			$this->aliases[$this->getContextKey($newContext)] = $this->aliases[$contextKey];
		}

		return $this;
	}

	public function offsetUnset($context)
	{
		$contextKey = $this->getContextKey($context);

		if (isset($this->aliases[$contextKey]) === true)
		{
			unset($this->aliases[$contextKey]);
		}

		return $this;
	}

	public function offsetExists($context)
	{
		return (isset($this->aliases[$this->getContextKey($context)]) === true);
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

	public function from($context)
	{
		$this->context = $context;

		return $this;
	}

	public function alias($keyword)
	{
		$this->keyword = $keyword;

		return $this;
	}

	public function to($alias)
	{
		$this->aliasKeyword($this->keyword, $alias, $this->context);

		$this->keyword = null;

		return $this;
	}

	public function aliasKeyword($keyword, $alias, $context = null)
	{
		$this->aliases[$this->getContextKey($context)][$this->getAliasKey($alias)] = $keyword;

		return $this;
	}

	public function resolveAlias($alias, $context = null)
	{
		$aliasKey = $this->getAliasKey($alias);
		$contextKey = $this->getContextKey($context);

		return (isset($this->aliases[$contextKey]) === false || isset($this->aliases[$contextKey][$aliasKey]) === false ? $alias : $this->aliases[$contextKey][$aliasKey]);
	}

	private function getAliasKey($alias)
	{
		return strtolower($alias);
	}

	private function getContextKey($context)
	{
		if ($context === null && $this->context !== null)
		{
			$context = $this->context;

			$this->context = null;
		}

		return ($context == '' ? '' : strtolower($this->resolver->resolve($context) ?: $context));
	}
}
