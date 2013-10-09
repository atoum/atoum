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

	public function __construct($function = null, array $arguments = null)
	{
		if ($function !== null)
		{
			$function = static::normalizeFunction($function);
		}

		$this->function = $function;
		$this->arguments = $arguments;

		$this->setDecorator();
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
		$this->function = static::normalizeFunction($function);

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

	public function isEqualTo(self $call)
	{
		$isEqual = false;

		if ($this->function !== null && $this->function == $call->function)
		{
			if (is_array($this->arguments) === false)
			{
				$isEqual = true;
			}
			else
			{
				if (sizeof($this->arguments) <= 0)
				{
					$isEqual = ($call->arguments === $this->arguments);
				}
				else if (is_array($call->arguments) === true)
				{
					$callback = function($a, $b) {
						return ($a == $b ? 0 : -1);
					};

					$isEqual = ($this->arguments == array_uintersect_uassoc($call->arguments, $this->arguments, $callback, $callback));
				}
				else
				{
					$isEqual = false;
				}
			}
		}

		return $isEqual;
	}

	public function isIdenticalTo(self $call)
	{
		$isIdentical = $this->isEqualTo($call);

		if ($isIdentical === true && sizeof($this->arguments) > 0 && $call->arguments !== null)
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

	public static function normalizeFunction($function)
	{
		$function = strtolower($function);

		if ($function === '')
		{
			throw new exceptions\logic\invalidArgument('Function must not be empty');
		}

		return $function;
	}
}
