<?php

namespace mageekguy\atoum;

class dependencies implements \arrayAccess
{
	protected $value = null;
	protected $dependencies = array();

	public function __construct($mixed = null)
	{
		if ($mixed !== null)
		{
			$this->setValue($mixed);
		}
	}

	public function __invoke(array $dependencies = array())
	{
		return $this->build($dependencies);
	}

	public function build(array $dependencies = array())
	{
		if ($this->value === null)
		{
			throw new dependencies\exception('Value is undefined');
		}

		foreach ($dependencies as $name => $value)
		{
			$this->setDependence($name, $value);
		}

		return ($this->value instanceof \closure === false ? $this->value : $this->value->__invoke($this));
	}

	public function getValue($dependence = null)
	{
		switch (true)
		{
			case $dependence === null:
				return $this->value;

			case isset($this->dependencies[$dependence]) === false:
				return null;

			default:
				return $this->dependencies[$dependence]->getValue();
		}
	}

	public function setValue($mixed)
	{
		$this->value = $mixed;

		return $this;
	}

	public function setDependence($name, $mixed)
	{
		return $this->offsetSet($name, $mixed);
	}

	public function getDependence($name)
	{
		return $this->offsetGet($name);
	}

	public function dependenceExists($name)
	{
		return $this->offsetExists($name);
	}

	public function unsetDependence($name)
	{
		return $this->offsetUnset($name);
	}

	public function offsetSet($name, $mixed)
	{
		if ($mixed instanceof self)
		{
			$this->dependencies[$name] = $mixed;
		}
		else
		{
			$this[$name]->setValue($mixed);
		}

		return $this;
	}

	public function offsetGet($name = null)
	{
		if (isset($this->dependencies[$name]) === false)
		{
			$this->dependencies[$name] = new static();
		}

		return $this->dependencies[$name];
	}

	public function offsetExists($name)
	{
		return (isset($this->dependencies[$name]) === true && $this->dependencies[$name]->value !== null);
	}

	public function offsetUnset($name)
	{
		if (isset($this->dependencies[$name]) === true)
		{
			unset($this->dependencies[$name]);
		}

		return $this;
	}
}

class_alias(__NAMESPACE__ . '\dependencies', __NAMESPACE__ . '\dependency');
