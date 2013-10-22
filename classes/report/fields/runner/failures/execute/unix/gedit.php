<?php

namespace atoum\report\fields\runner\failures\execute\unix;

use
	atoum,
	atoum\report\fields\runner\failures
;

class gedit extends failures\execute
{
	public function __construct()
	{
		parent::__construct('gedit %1$s +%2$d > /dev/null &');
	}
}
