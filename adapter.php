<?php

namespace mageekguy\tests\unit;

class adapter
{
	protected $callables = array();

	public function setCallable($name, \callable $callable)
	{
		$this->callables[$name] = $callable;
		return $this;
	}

	public function __call($method, $arguments)
	{
		return (isset($this->callables[$method]) === false ? call_user_func_array($method, $arguments) : $this->callables[$method]->invoke($arguments));
	}
}

?>
