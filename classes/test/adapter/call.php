<?php

namespace mageekguy\atoum\test\adapter;

use
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\test\adapter\call
;

class call
{
	protected $function = null;
	protected $arguments = null;
	protected $decorator = null;

	public function __construct($function = null, array $arguments = null)
	{
		$this->function = $function;
		$this->arguments = $arguments;

		$this->setDecorator();
	}

	public function getFunction()
	{
		return $this->function;
	}

	public function setFunction($function)
	{
		$this->function = $function;

		return $this;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function setArguments(array $arguments)
	{
		$this->arguments = $arguments;

		return $this;
	}

	public function setDecorator(call\decorator $decorator = null)
	{
		$this->decorator = $decorator ?: new call\decorator();

		return $this;
	}

	public function getDecorator()
	{
		return $this->decorator;
	}

	public function find(adapter $adapter)
	{
		$calls = $adapter->getCalls($this->function) ?: array();

		if (sizeof($calls) > 0 && $this->arguments !== null)
		{
			$arguments = $this->arguments;

			if ($arguments === array())
			{
				$filter = function($callArguments) use ($arguments) {
					return ($arguments === $callArguments);
				};
			}
			else
			{
				$callback = function($a, $b) {
					return ($a == $b ? 0 : -1);
				};

				$filter = function($callArguments) use ($arguments, $callback) {
					return ($arguments == array_uintersect_uassoc($callArguments, $arguments, $callback, $callback));
				};
			}

			$calls = array_filter($calls, $filter);
		}

		return $calls;
	}

	public function findFirst(adapter $adapter)
	{
		return (array_slice($this->find($adapter), 0, 1, true) ?: null);
	}

	public function findLast(adapter $adapter)
	{
		return (array_slice($this->find($adapter), -1, 1, true) ?: null);
	}

	public function isEqualTo(self $call)
	{
		switch (true)
		{
			case $this->function !== null && $this->arguments === array() && $call->function !== null && $call->arguments !== null:
				return ($this->function == $call->function && $call->arguments === array());

			case $this->function !== null && $this->arguments !== null && $call->function !== null && $call->arguments !== null:
				$callback = function($a, $b) {
					return ($a == $b ? 0 : -1);
				};

				return ($this->function == $call->function && ($this->arguments == array_uintersect_uassoc($call->arguments, $this->arguments, $callback, $callback)));

			case $this->function !== null && $this->arguments === null && $call->function !== null:
				return  ($this->function == $call->function);

			default:
				return false;
		}
	}

	public function isIdenticalTo(self $call)
	{
		return ($this->isEqualTo($call) === false ? false : $this->arguments === null || ($this->arguments === $call->arguments));
	}
}
