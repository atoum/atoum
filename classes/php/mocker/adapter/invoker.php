<?php

namespace mageekguy\atoum\php\mocker\adapter;

use mageekguy\atoum\test\adapter;

class invoker extends adapter\invoker
{
	public function invoke(array $arguments = array(), $call = 0)
	{
		return parent::invoke($arguments, $call);
	}
}
