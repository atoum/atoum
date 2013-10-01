<?php

namespace mageekguy\atoum\test\adapter\call;

use
	mageekguy\atoum\test\adapter\call\arguments
;

class decorator
{
	protected $argumentsDecorator = null;

	public function __construct()
	{
		$this->setArgumentsDecorator();
	}

	public function getArgumentsDecorator()
	{
		return $this->argumentsDecorator;
	}

	public function setArgumentsDecorator(arguments\decorator $decorator = null)
	{
		$this->argumentsDecorator = $decorator ?: new arguments\decorator();

		return $this;
	}
}
