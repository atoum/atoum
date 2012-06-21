<?php

namespace mageekguy\atoum\dependencies;

class injector implements \arrayAccess
{
	protected $closure = null;
	protected $arguments = array();

	public function __construct(\closure $closure)
	{
		$this->closure = $closure;
	}

	public function __invoke()
	{
		return call_user_func_array($this->closure, func_get_args() ?: $this->arguments);
	}

	public function offsetSet($argument, $value)
	{
		return $this->setArgument($argument, $value);
	}

	public function offsetGet($argument)
	{
	}

	public function offsetUnset($argument)
	{
	}

	public function offsetExists($argument)
	{
	}

	public function getClosure()
	{
		return $this->closure;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function setArgument($name, $value)
	{
		$this->arguments[$name] = $value;

		ksort($this->arguments, SORT_NUMERIC);

		return $this;
	}
}
