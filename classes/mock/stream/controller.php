<?php

namespace mageekguy\atoum\mock\stream;

use
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions
;

class controller extends test\adapter
{
	protected $from = null;

	public function __get($method)
	{
		return parent::__get(self::mapMethod($method));
	}

	public function __set($method, $return)
	{
		return parent::__set(self::mapMethod($method), $return);

		return $this;
	}

	public function __isset($method)
	{
		return parent::__isset(self::mapMethod($method));
	}

	public function invoke($method, array $arguments = array())
	{
		return (isset($this->{$method = self::mapMethod($method)}) === false ? null : parent::invoke($method, $arguments));
	}

	protected static function mapMethod($method)
	{
		switch ($method)
		{
			case 'mkdir':
			case 'rmdir':
			case 'rename':
			case 'unlink':
			case '__construct':
			case 'stream_set_option':
				return $method;

			case 'closedir':
			case 'dir_closedir':
				return 'dir_closedir';

			case 'opendir':
			case 'dir_opendir':
				return 'dir_opendir';

			case 'readdir':
			case 'dir_readdir':
				return 'dir_readdir';

			case 'rewinddir':
			case 'dir_rewinddir':
				return 'dir_rewinddir';

			case 'select':
			case 'stream_cast':
				return 'stream_cast';

			case 'fclose':
			case 'stream_close':
				return 'stream_close';

			case 'feof':
			case 'stream_eof':
				return 'stream_eof';

			case 'fflush':
			case 'stream_flush':
				return 'stream_flush';

			case 'flock':
			case 'stream_lock':
				return 'stream_lock';

			case 'touch':
			case 'chmod':
			case 'chown':
			case 'chgrp':
			case 'stream_metadata':
				return 'stream_metadata';

			case 'fopen':
			case 'stream_open':
				return 'stream_open';

			case 'fread':
			case 'fgets':
			case 'stream_read':
				return 'stream_read';

			case 'fseek':
			case 'stream_seek':
				return 'stream_seek';

			case 'fstat':
			case 'stream_stat':
				return 'stream_stat';

			case 'ftell':
			case 'stream_tell':
				return 'stream_tell';

			case 'fwrite':
			case 'stream_write':
				return 'stream_write';

			case 'stat':
			case 'url_stat':
				return 'url_stat';

			default:
				throw new exceptions\logic\invalidArgument('Method streamWrapper::' . $method . '() does not exist');
		}
	}
}

?>
