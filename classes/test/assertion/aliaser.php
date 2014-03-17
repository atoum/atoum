<?php

namespace mageekguy\atoum\test\assertion;

use
	atoum\asserter
;

class aliaser
{
	protected $resolver = null;
	protected $classes = array();
	protected $methods = array();

	private $class = null;
	private $target = null;

	public function __construct(asserter\resolver $resolver = null)
	{
		$this->setResolver($resolver);
	}

	public function __set($class, $alias)
	{
		return $this->alias($alias)->to($class);
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

	public function from($class)
	{
		$this->class = $class;

		return $this;
	}

	public function alias($target)
	{
		$this->target = $target;

		return $this;
	}

	public function to($alias)
	{
		if ($this->target !== null)
		{
			if ($this->class === null)
			{
				$this->aliasClass($this->target, $alias);
			}
			else
			{
				$this->aliasMethod($this->class, $this->target, $alias);
			}
		}

		return $this;
	}

	public function aliasClass($class, $alias)
	{
		$this->classes[strtolower($alias)] = strtolower($class);

		return $this;
	}

	public function resolveClass($class)
	{
		$class = strtolower($class);

		return (isset($this->classes[$class]) === false ? $class : $this->classes[$class]);
	}

	public function getClassAliases()
	{
		return $this->classes;
	}

	public function resetClassAliases()
	{
		$this->classes = array();

		return $this;
	}

	public function aliasMethod($class, $method, $alias)
	{
		$this->methods[strtolower($this->resolver->resolve($class))][strtolower($alias)] = strtolower($method);

		return $this;
	}

	public function resolveMethod($class, $alias)
	{
		$class = strtolower($this->resolver->resolve($class));
		$alias = strtolower($alias);

		return (isset($this->methods[$class]) === false || isset($this->methods[$class][$alias]) === false ? $alias : $this->methods[$class][$alias]);
	}

	public function getMethodAliases()
	{
		return $this->methods;
	}

	public function resetMethodAliases()
	{
		$this->methods = array();

		return $this;
	}
}
