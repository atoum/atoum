<?php

namespace atoum\report\fields\runner\failures\execute\unix;

use
	atoum,
	atoum\report\fields\runner\failures
;

class gvim extends failures\execute
{
	public function __construct()
	{
		parent::__construct('gvim +%2$d %1$s > /dev/null &');
	}
}
