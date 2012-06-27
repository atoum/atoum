<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\exceptions
;

class adapter implements adapter\definition
{
	public function __call($functionName, $arguments)
	{
		return $this->invoke($functionName, $arguments);
	}

	public function invoke($functionName, array $arguments = array())
	{
		return call_user_func_array($functionName, $arguments);
	}
}
