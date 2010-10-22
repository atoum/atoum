<?php

namespace mageekguy\atoum;

use mageekguy\atoum;

class registry extends atoum\singleton
{
	protected $values = array();

	public function __set($key, $value)
	{
		if (isset($this->values[$key]) === true)
		{
			throw new \logicException('Key \'' . $key . '\' is already in registry');
		}

		$this->values[$key] = $value;

		return $this;
	}

	public function __get($key)
	{
		if (isset($this->{$key}) === false)
		{
			throw new \logicException('Key \'' . $key . '\' is not in registry');
		}

		return $this->values[$key];
	}

	public function __isset($key)
	{
		return (isset($this->values[$key]) === true);
	}

	public function __unset($key)
	{
		if (isset($this->{$key}) === false)
		{
			throw new \logicException('Key \'' . $key . '\' is not in registry');
		}

		unset($this->values[$key]);

		return $this;
	}
}

?>
