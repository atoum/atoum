<?php

namespace mageekguy\atoum;

class adapter
{
	protected $functions = array();
	protected $calls = array();

	public function __set($functionName, \closure $closure)
	{
		$this->functions[$functionName] = $closure;
	}

	public function __get($functionName)
	{
		return (isset($this->{$functionName}) === false ? null : $this->functions[$functionName]);
	}

	public function __isset($functionName)
	{
		return (isset($this->functions[$functionName]) === true);
	}

	public function __call($functionName, $arguments)
	{
		$this->calls[$functionName][] = $arguments;

		return (isset($this->functions[$functionName]) === false ? call_user_func_array($functionName, $arguments) : $this->functions[$functionName]->__invoke($arguments));
	}

	public function getCalls($functionName = null)
	{
		return ($functionName === null ?  $this->calls : (isset($this->calls[$functionName]) === false ? null : $this->calls[$functionName]));
	}
}

?>
