<?php

namespace mageekguy\atoum\mock\streams\fs;

use
	mageekguy\atoum\mock\stream
;

class file extends stream
{
	protected static function getController($stream)
	{
		return new file\controller($stream);
	}
}
