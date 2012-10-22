<?php

namespace mageekguy\atoum\test\exceptions;

use
	mageekguy\atoum\exceptions
;

class skip extends exceptions\runtime
{
	public function __construct($message)
	{
		parent::__construct($message);
	}
}
