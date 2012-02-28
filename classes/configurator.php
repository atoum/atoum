<?php

namespace mageekguy\atoum;

class configurator
{
	protected $script = null;
	protected $methods = array();

	public function __construct(scripts\runner $script)
	{
		$this->script = $script;

		foreach ($this->script->getHelp() as $help)
		{
			list($arguments, $values) = $help;

			foreach ($arguments as $argument)
			{
				$methodName = preg_replace('/-(.)/e', 'ucfirst(\'\1\')', ltrim($argument, '-'));

				if ($values === null)
				{
					$this->methods[$methodName] = $argument;
				}
				else
				{
					$this->methods['set' . ucfirst($methodName)] = $argument;
				}
			}
		}
	}

	public function __call($method, $arguments)
	{
		switch (true)
		{
			case isset($this->methods[$method]):
				$this->script->getArgumentsParser()->invokeHandlers($this->script, $this->methods[$method], $arguments);
				return $this;

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
