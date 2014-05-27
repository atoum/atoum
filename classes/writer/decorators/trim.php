<?php

namespace mageekguy\atoum\writer\decorators;

use
	mageekguy\atoum\writer
;

class trim implements writer\decorator
{
	public function decorate($message)
	{
		return trim($message);
	}
}
