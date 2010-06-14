<?php

namespace mageekguy\tests\unit;

class adapter
{
	protected $callables = array();

	public function setFunction($function, \closure $callable)
	{
		$this->callables[$function] = $callable;
		return $this;
	}

	public function __set($function, \closure $callable)
	{
		$this->setFunction($function, $callable);
	}

	public function __call($method, $arguments)
	{
		return (isset($this->callables[$method]) === false ? call_user_func_array($method, $arguments) : $this->callables[$method]($arguments));
	}
}

?>
