<?php

namespace mageekguy\atoum\writer\decorators;

use
	mageekguy\atoum\writer
;

class eol implements writer\decorator
{
	public function decorate($message)
	{
		return $message . PHP_EOL;
	}
}
