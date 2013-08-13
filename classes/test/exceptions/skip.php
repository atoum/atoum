<?php

namespace atoum\test\exceptions;

use
	atoum\exceptions
;

class skip extends exceptions\runtime
{
	public function __construct($message)
	{
		parent::__construct($message);
	}
}
