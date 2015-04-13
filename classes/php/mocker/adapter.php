<?php

namespace mageekguy\atoum\php\mocker;

use
	mageekguy\atoum\test,
	mageekguy\atoum\php\mocker
;

class adapter extends test\adapter
{
	protected function setInvoker($functionName, \closure $factory = null)
	{
		if ($factory === null)
		{
			$factory = function($functionName) { return new mocker\adapter\invoker($functionName); };
		}

		return parent::setInvoker($functionName, $factory);
	}
}
