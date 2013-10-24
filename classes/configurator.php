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
				$this->methods[strtolower(str_replace('-', '', $argument))] = $argument;
			}
		}
	}

	public function __call($method, $arguments)
	{
		$keyMethod = strtolower($method);

		if (isset($this->methods[$keyMethod]) === true)
		{
			if (isset($arguments[0]) === true && is_array($arguments[0]) === true)
			{
				$arguments = $arguments[0];
			}

			$this->script->getArgumentsParser()->invokeHandlers($this->script, $this->methods[$keyMethod], $arguments);

			return $this;
		}
		else
		{
			if (method_exists($this->script, $keyMethod) === false)
			{
				throw new exceptions\runtime\unexpectedValue('Method \'' . $method . '\' is unavailable');
			}

			$return = call_user_func_array(array($this->script, $keyMethod), $arguments);

			return ($return === $this->script ? $this : $return);
		}
	}

	public function getScript()
	{
		return $this->script;
	}
}
