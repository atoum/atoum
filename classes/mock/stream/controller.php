<?php

namespace mageekguy\atoum\mock\stream;

use
	mageekguy\atoum\mock,
	mageekguy\atoum\exceptions
;

class controller
{
	protected $methods = array();

	public function __set($method, $return)
	{
		$this->checkMethod($method)->methods[$method] = ($return instanceof \closure ? $return : function() use ($return) { return $return; });

		return $this;
	}

	public function invoke($method, array $arguments = array())
	{
		return (isset($this->checkMethod($method)->methods[$method]) === false ? null : call_user_func_array($this->methods[$method], $arguments));
	}

	protected function checkMethod($method)
	{
		switch ($method)
		{
			case '__construct':
			case 'dir_closedir':
			case 'dir_opendir':
			case 'dir_readdir':
			case 'dir_rewinddir':
			case 'mkdir':
			case 'rename':
			case 'rmdir':
			case 'stream_cast':
			case 'stream_close':
			case 'stream_eof':
			case 'stream_flush':
			case 'stream_lock':
			case 'stream_metadata':
			case 'stream_open':
			case 'stream_read':
			case 'stream_seek':
			case 'stream_set_option':
			case 'stream_stat':
			case 'stream_tell':
			case 'stream_write':
			case 'unlink':
			case 'url_stat':
				return $this;

			default:
				throw new exceptions\logic\invalidArgument('Method streamWrapper::' . $method . '() does not exist');
		}
	}
}

?>
