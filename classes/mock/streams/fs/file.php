<?php

namespace atoum\mock\streams\fs;

use
	atoum\mock\stream
;

class file extends stream
{
	protected static function getController($stream)
	{
		return new file\controller($stream);
	}
}
