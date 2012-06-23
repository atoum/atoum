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
		$this->setArgumentsDecorator(new arguments\decorator());
	}

	public function setArgumentsDecorator(arguments\decorator $decorator)
	{
		$this->argumentsDecorator = $decorator;

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
