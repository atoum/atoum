<?php

namespace mageekguy\atoum;

use \mageekguy\atoum\exceptions;

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
		if (is_callable($functionName) === false)
		{
			throw new exceptions\logic\argument('Function \'' . $functionName . '()\' is not callable by an adapter');
		}

		$this->calls[$functionName][] = $arguments;

		return call_user_func_array(isset($this->functions[$functionName]) === false ? $functionName : $this->functions[$functionName], $arguments);
	}

	public function getCalls($functionName = null)
	{
		return ($functionName === null ?  $this->calls : (isset($this->calls[$functionName]) === false ? null : $this->calls[$functionName]));
	}
}

?>
