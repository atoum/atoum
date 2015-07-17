<?php

namespace mageekguy\atoum\test\adapter\call;

use
	mageekguy\atoum\test\adapter\call,
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

	public function decorate(call $call)
	{
		$string = '';

		$function = $call->getFunction();

		if ($function !== null)
		{
			$string = $function . '(';

			$arguments = $call->getArguments();

			if ($arguments === null)
			{
				$string .= '*';
			}
			else
			{
				$string .= $this->argumentsDecorator->decorate($call->getArguments());
			}

			$string .= ')';
		}

		return $string;
	}
}
