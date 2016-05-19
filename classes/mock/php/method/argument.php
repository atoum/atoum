<?php

namespace mageekguy\atoum\mock\php\method;

class argument
{
	protected $type = null;
	protected $isReference = false;
	protected $name = '';
	protected $defaultValue = null;
	protected $defaultValueIsSet = false;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function __toString()
	{
		$string = '$' . $this->name;

		if ($this->isReference === true)
		{
			$string = '& ' . $string;
		}

		if ($this->type !== null)
		{
			$string = $this->type . ' ' . $string;
		}

		if ($this->defaultValueIsSet === true)
		{
			$string .= '=' . var_export($this->defaultValue, true);
		}

		return $string;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getVariable()
	{
		return '$' . $this->name;
	}

	public function isObject($type)
	{
		$this->type = $type;

		return $this;
	}

	public function isArray()
	{
		$this->type = 'array';

		return $this;
	}

	public function isUntyped()
	{
		$this->type = null;

		return $this;
	}

	public function isReference()
	{
		$this->isReference = true;

		return $this;
	}

	public function setDefaultValue($defaultValue)
	{
		$this->defaultValue = $defaultValue;
		$this->defaultValueIsSet = true;

		return $this;
	}

	public static function get($name)
	{
		return new static($name);
	}
}
