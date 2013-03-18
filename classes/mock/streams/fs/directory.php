<?php

namespace mageekguy\atoum\mock\streams\fs;

use
	mageekguy\atoum\mock\stream
;

class directory extends stream
{
	protected static function getController($stream)
	{
		return new directory\controller($stream);
	}
}
