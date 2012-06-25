<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\dependencies
;

class dependencies implements \arrayAccess, \countable, \serializable
{
	protected $injector = null;
	protected $arguments = array();
	protected $dependencies = array();

	public function __construct($injector = null)
	{
		if ($injector !== null && $injector instanceof \closure === false)
		{
			$injector = function() use ($injector) { return $injector; };
		}

		$this->injector = $injector;
	}

	public function __invoke(array $arguments = array())
	{
		foreach ($arguments as $name => $value)
		{
			$this->{$name} = $value;
		}

		return (($injector = $this->injector) === null ? null : $injector($this));
	}

	public function __set($argument, $value)
	{
		return $this->setArgument($argument, $value);
	}

	public function __get($argument)
	{
		return $this->getArgument($argument);
	}

	public function __isset($argument)
	{
		return $this->argumentExists($argument);
	}

	public function offsetGet($name)
	{
		return $this->getDependence($name);
	}

	public function offsetSet($name, $mixed)
	{
		return $this->setDependence($name, $mixed instanceof self ? $mixed : new self($mixed));
	}

	public function offsetUnset($name)
	{
		return $this->unsetDependence($name);
	}

	public function offsetExists($name)
	{
		return $this->dependenceExists($name);
	}

	public function count()
	{
		return sizeof($this->dependencies);
	}

	public function serialize() {}

	public function unserialize($string) {}

	public function getDependence($name)
	{
		return (isset($this->dependencies[$name]) === false ? null : $this->dependencies[$name]);
	}

	public function setDependence($name, dependencies $dependence)
	{
		$this->dependencies[$name] = $dependence;

		return $this;
	}

	public function dependenceExists($name)
	{
		return (isset($this->dependencies[$name]) === true);
	}

	public function unsetDependence($name)
	{
		if ($this->dependenceExists($name) === true)
		{
			unset($this->dependencies[$name]);
		}

		return $this;
	}

	public function setArgument($name, $value)
	{
		$this->arguments[$name] = $value;

		return $this;
	}

	public function getArgument($name)
	{
		if ($this->argumentExists($name) === false)
		{
			throw new dependencies\exception('Argument \'' . $name . '\' is undefined');
		}

		return $this->arguments[$name];
	}

	public function argumentExists($name)
	{
		return array_key_exists($name, $this->arguments);
	}

	public function unsetArgument($name)
	{
		if ($this->argumentExists($name) === true)
		{
			unset($this->arguments[$name]);
		}

		return $this;
	}
}

class_alias(__NAMESPACE__ . '\dependencies', __NAMESPACE__ . '\dependence');
