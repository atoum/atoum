<?php

namespace mageekguy\atoum\mock\streams;

use
	mageekguy\atoum\mock\stream
;

class file extends stream
{
	public static function get($stream = null)
	{
		$file = parent::get($stream);

		$file->stat = array('mode' => 33188);
		$file->fopen = true;
		$file->fread[1] = '';
		$file->fread[2] = false;
		$file->fclose = true;
		$file->rename = true;
		$file->unlink = true;

		return $file;
	}

	protected static function getController($stream)
	{
		return new file\controller($stream);
	}
}
