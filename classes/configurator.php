<?php

namespace mageekguy\atoum;

class configurator
{
	public function __construct(scripts\runner $script)
	{
		$this->script = $script;
	}

	public function __call($method, $arguments)
	{
		switch (true)
		{
			case method_exists($this->script->getRunner(), $method):
				return call_user_func_array(array($this->script->getRunner(), $method), $arguments);

			case method_exists($this->script, $method):
				return call_user_func_array(array($this->script, $method), $arguments);

			default:
				throw new exceptions\runtime\unexpectedValue('Method \'' . $method . '\' is unavailable');
		}
	}

	public function getScript()
	{
		return $this->script;
	}
}

?>
