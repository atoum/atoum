<?php

namespace mageekguy\atoum\dependencies;

use
	mageekguy\atoum\dependencies\injector\exception
;

class injector implements \arrayAccess
{
	protected $closure = null;
	protected $availableArguments = array();

	protected $arguments = array();

	public function __construct(\closure $closure)
	{
		$this->closure = $closure;

		$reflectedClosure = new \reflectionFunction($this->closure);

		foreach ($reflectedClosure->getParameters() as $argument)
		{
			$this->availableArguments[$argument->getName()] = $argument->isDefaultValueAvailable();
		}
	}

	public function __invoke()
	{
		$arguments = func_get_args();

		if (sizeof($arguments) <= 0)
		{
			foreach ($this->availableArguments as $name => $hasDefaultValue)
			{
				if (isset($this->arguments[$name]) === true)
				{
					$arguments[] = $this->arguments[$name];
				}
				else if ($hasDefaultValue === false)
				{
					throw new exception('Argument \'' . $name . '\' is missing');
				}
			}
		}

		return call_user_func_array($this->closure, $arguments);
	}

	public function __set($argument, $value)
	{
		return $this->setArgument($argument, $value);
	}

	public function __get($argument)
	{
		return $this->getArgument($argument);
	}

	public function __isset($argument)
	{
		return $this->argumentExists($argument);
	}

	public function __unset($argument)
	{
		return $this->unsetArgument($argument);
	}

	public function offsetSet($argument, $value)
	{
		return $this->setArgument($argument, $value);
	}

	public function offsetGet($argument)
	{
		return $this->getArgument($argument);
	}

	public function offsetExists($argument)
	{
		return $this->argumentExists($argument);
	}

	public function offsetUnset($argument)
	{
		return $this->unsetArgument($argument);
	}

	public function getClosure()
	{
		return $this->closure;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function getAvailableArguments()
	{
		return array_keys($this->availableArguments);
	}

	public function getArgument($name)
	{
		if ($this->argumentExists($name) === false)
		{
			throw new exception('Argument \'' . $name . '\' is undefined');
		}

		return $this->arguments[$name];
	}

	public function setArgument($name, $value)
	{
		if (isset($this->availableArguments[$name]) === true)
		{
			$this->arguments[$name] = $value;
		}

		return $this;
	}

	public function argumentExists($name)
	{
		return (isset($this->arguments[$name]) === true);
	}

	public function unsetArgument($name)
	{
		if ($this->argumentExists($name) === false)
		{
			throw new exception('Argument \'' . $name . '\' is undefined');
		}

		unset($this->arguments[$name]);

		return $this;
	}
}
