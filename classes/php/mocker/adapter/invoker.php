<?php

namespace mageekguy\atoum\php\mocker\adapter;

use
	atoum\test\adapter
;

class invoker extends adapter\invoker
{
	public function invoke(array $arguments = array(), $call = 0)
	{
		foreach ($arguments as $key => $argument)
		{
			$arguments[$key] = & $argument;
		}

		return parent::invoke($arguments, $call);
	}
}
