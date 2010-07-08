<?php

namespace mageekguy\tests\unit;

class adapter
{
	protected $functions = array();

	public function __set($function, \closure $closure)
	{
		$this->functions[$function] = $closure;
	}

	public function __call($function, $arguments)
	{
		return (isset($this->functions[$function]) === false ? call_user_func_array($function, $arguments) : $this->functions[$function]->__invoke($arguments));
	}
}

?>
