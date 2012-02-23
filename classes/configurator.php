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
				call_user_func_array(array($this->script->getRunner(), $method), $arguments);
				return $this;

			case method_exists($this->script, $method):
				call_user_func_array(array($this->script, $method), $arguments);
				return $this;

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
