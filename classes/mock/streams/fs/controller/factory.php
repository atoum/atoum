<?php

namespace mageekguy\atoum\mock\streams\fs\controller;

use
	mageekguy\atoum\mock\streams\fs\controller
;

class factory
{
	public function build($name)
	{
		return new controller($name);
	}
}
