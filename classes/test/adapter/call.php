<?php

namespace mageekguy\atoum\test\adapter;

use
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\test\adapter\call
;

class call
{
	protected $function = null;
	protected $arguments = null;
	protected $decorator = null;

	public function __construct($function = null, array $arguments = null, call\decorator $decorator = null)
	{
		if ($function !== null)
		{
			$this->setFunction($function);
		}

		$this->arguments = $arguments;

		$this->setDecorator($decorator);
	}

	public function __toString()
	{
		return $this->decorator->decorate($this);
	}

	public function getFunction()
	{
		return $this->function;
	}

	public function setFunction($function)
	{
		$function = (string) $function;

		if ($function === '')
		{
			throw new exceptions\logic\invalidArgument('Function must not be empty');
		}

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

	public function unsetArguments()
	{
		$this->arguments = null;

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

	public function isEqualTo(call $call)
	{
		$isEqual = false;

		if ($this->function !== null && $this->function == $call->function)
		{
			$isEqual = ($this->arguments === null);

			if ($isEqual === false && $call->arguments !== null)
			{
				if (sizeof($this->arguments) <= 0)
				{
					$isEqual = (sizeof($call->arguments) <= 0);
				}
				else if (sizeof($this->arguments) <= sizeof($call->arguments))
				{
					$callback = function($a, $b) {
						return ($a == $b ? 0 : -1);
					};

					$isEqual = ($this->arguments == array_uintersect_uassoc($call->arguments, $this->arguments, $callback, $callback));
				}
			}
		}

		return $isEqual;
	}

	public function isIdenticalTo(call $call)
	{
		$isIdentical = $this->isEqualTo($call);

		if ($isIdentical === true && $this->arguments !== null && $call->arguments !== null)
		{
			$callback = function($a, $b) {
				return ($a === $b ? 0 : -1);
			};

			$isIdentical = ($this->arguments === array_uintersect_uassoc($call->arguments, $this->arguments, $callback, $callback));
		}

		return $isIdentical;
	}

	public function isFullyQualified()
	{
		return ($this->function !== null && $this->arguments !== null);
	}
}
