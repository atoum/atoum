<?php

namespace mageekguy\atoum;

class depedencies implements \arrayAccess
{
	protected $injectors = array();

	public function __invoke()
	{
		return $this;
	}

	public function getInjectors()
	{
		return $this->injectors;
	}

	public function offsetSet($mixed, $injector)
	{
		if ($injector instanceof \closure === false && $injector instanceof self === false)
		{
			$injector = function() use ($injector) { return $injector; };
		}

		$key = self::getKey($mixed);

		$this->injectors[$key] = $injector;

		return $this;
	}

	public function offsetGet($mixed)
	{
		$key = self::getKey($mixed);

		return ($this->offsetExists($key) === false ? null : $this->injectors[$key]);
	}

	public function offsetUnset($mixed)
	{
		$key = self::getKey($mixed);

		if ($this->offsetExists($key) === true)
		{
			unset($this->injectors[$key]);
		}

		return $this;
	}

	public function offsetExists($mixed)
	{
		return isset($this->injectors[self::getKey($mixed)]);
	}

	protected static function getKey($value)
	{
		return is_object($value) ? get_class($value) : (string) $value;
	}
}

?>
