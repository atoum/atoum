<?php

namespace mageekguy\atoum\mock\php;

use
	mageekguy\atoum\exceptions
;

class method
{
	protected $returnReference = false;
	protected $name = '';
	protected $isConstructor = false;
	protected $arguments = array();

	public function __construct($name)
	{
		$this->name = $name;

		$this->isConstructor = ($name == __FUNCTION__);
	}

	public function __toString()
	{
		$string = 'public function ';

		if ($this->returnReference === true)
		{
			$string .= '& ';
		}

		$string .= $this->name . '(' . $this->getArgumentsAsString() . ')';

		return $string;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function getName()
	{
		return $this->name;
	}

	public function isConstructor()
	{
		return $this->isConstructor;
	}

	public function returnReference()
	{
		if ($this->isConstructor === true)
		{
			throw new exceptions\logic('Constructor can not return a reference');
		}

		$this->returnReference = true;

		return $this;
	}

	public function addArgument(method\argument $argument)
	{
		$this->arguments[] = $argument;

		return $this;
	}

	public function getArgumentsAsString()
	{
		$arguments = $this->arguments;

		array_walk($arguments, function(& $value) { $value = (string) $value; });

		return join(', ', $arguments);
	}

	public static function get($name)
	{
		return new static($name);
	}
}
