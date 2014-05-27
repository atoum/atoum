<?php

namespace mageekguy\atoum\writer\decorators;

use
	mageekguy\atoum\writer
;

class rtrim implements writer\decorator
{
	public function decorate($message)
	{
		return rtrim($message);
	}
}
