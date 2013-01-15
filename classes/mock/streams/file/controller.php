<?php

namespace mageekguy\atoum\mock\streams\file;

use
	mageekguy\atoum\mock\stream
;

class controller extends stream\controller
{
	public function canNotBeOpened()
	{
		return parent::__set('fopen', false);
	}

	public function canBeOpened()
	{
		return parent::__set('fopen', true);
	}
}
