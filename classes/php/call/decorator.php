<?php

namespace mageekguy\atoum\php\call;

use
	mageekguy\atoum\php
;

class decorator
{
	protected $argumentsDecorator = null;

	public function __construct()
	{
		$this->setArgumentsDecorator();
	}

	public function setArgumentsDecorator(arguments\decorator $decorator = null)
	{
		$this->argumentsDecorator = $decorator ?: new arguments\decorator();

		return $this;
	}

	public function getArgumentsDecorator()
	{
		return $this->argumentsDecorator;
	}

	public function decorate(php\call $call)
	{
		$string = $call->getFunction() . '(' . $this->argumentsDecorator->decorate($call->getArguments()) . ')';

		$object = $call->getObject();

		if ($object !== null)
		{
			$string = get_class($object) . '::' . $string;
		}

		return $string;
	}
}
