<?php

namespace mageekguy\atoum\php\mocker;

use
	atoum\test,
	atoum\php\mocker
;

class adapter extends test\adapter
{
	protected function setInvoker($functionName, \closure $factory = null)
	{
		if ($factory === null)
		{
			$factory = function() { return new mocker\adapter\invoker(); };
		}

		return parent::setInvoker($functionName, $factory);
	}
}
