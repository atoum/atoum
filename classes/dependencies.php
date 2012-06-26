<?php

namespace mageekguy\atoum;

class dependencies implements \arrayAccess
{
	protected $injector = null;
	protected $dependencies = array();

	public function __invoke(array $dependencies = array())
	{
		if ($this->injector === null)
		{
			throw new dependencies\exception('Injector is undefined');
		}

		foreach ($dependencies as $name => $value)
		{
			$this->setDependence($name, $value);
		}

		return ($this->injector instanceof \closure === false ? $this->injector : $this->injector->__invoke($this));
	}

	public function getInjector($dependence = null)
	{
		switch (true)
		{
			case $dependence === null:
				return $this->injector;

			case isset($this->dependencies[$dependence]) === false:
				return null;

			default:
				return $this->dependencies[$dependence]->getInjector();
		}
	}

	public function setInjector($mixed)
	{
		$this->injector = $mixed;

		return $this;
	}

	public function setDependence($name, $mixed)
	{
		if ($mixed instanceof self)
		{
			$this->dependencies[$name] = $mixed;
		}
		else
		{
			$this->dependencies[$name] = new static();
			$this->dependencies[$name]->setInjector($mixed);
		}

		return $this;
	}

	public function getDependence($name)
	{
		return ($this->dependenceExists($name) === false ? null : $this->dependencies[$name]);
	}

	public function dependenceExists($name)
	{
		return isset($this->dependencies[$name]);
	}

	public function unsetDependence($name)
	{
		if ($this->dependenceExists($name) === true)
		{
			unset($this->dependencies[$name]);
		}

		return $this;
	}

	public function offsetSet($name, $value)
	{
		return $this->setDependence($name, $value);
	}

	public function offsetGet($name)
	{
		return $this->getDependence($name);
	}

	public function offsetExists($name)
	{
		return $this->dependenceExists($name);
	}

	public function offsetUnset($name)
	{
		return $this->unsetDependence($name);
	}
}
