<?php

namespace mageekguy\atoum\mock\stream;

use
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions
;

class controller extends test\adapter
{
	protected $path = '';

	public function __construct($path)
	{
		parent::__construct();

		$this->path = (string) $path;
	}

	public function __toString()
	{
		return $this->getPath();
	}

	public function __get($method)
	{
		$method = static::mapMethod($method);

		return $this->setInvoker($method, function() use ($method) { return new invoker($method); });
	}

	public function __set($method, $value)
	{
		switch (strtolower($method))
		{
			case 'file_get_contents':
				if ($value === false)
				{
					$this->fopen = false;
				}
				else
				{
					$this->stat = array('mode' => 33188);
					$this->fopen = true;
					$this->fread[1] = $value;
					$this->fread[2] = false;
					$this->fclose = true;
				}
				return $this;

			case 'file_put_contents':
				$this->stat = array('mode' => 33188);
				$this->fopen = true;
				$this->fwrite = $value;
				$this->fclose = true;
				return $this;

			default:
				$method = static::mapMethod($method);

				switch ($method)
				{
					case 'dir_opendir':
						$this->dir_closedir = true;
						$this->dir_rewinddir = true;
						$this->dir_readdir = false;
						$this->url_stat = array('mode' => 16877);
						break;

					case 'dir_readdir':
						if ($value instanceof self)
						{
							$value = $value->getBasename();
						}
						break;
				}

				return parent::__set($method, $value);
		}
	}

	public function __isset($method)
	{
		return parent::__isset(static::mapMethod($method));
	}

	public function duplicate()
	{
		$controller = clone $this;

		$controller->path = & $this->path;
		$controller->calls = & $this->calls;
		$controller->invokers = & $this->invokers;

		return $controller;
	}

	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getBasename()
	{
		return basename($this->path);
	}

	public function invoke($method, array $arguments = array())
	{
		$method = static::mapMethod($method);

		if ($method === 'dir_rewinddir' && isset($this->{$method}) === true)
		{
			$this->resetCalls('dir_readdir');
		}

		return ($this->nextCallIsOverloaded($method) === false ? null : parent::invoke($method, $arguments));
	}

	protected static function mapMethod($method)
	{
		$method = strtolower($method);

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

			case 'ftruncate':
			case 'stream_truncate':
				return 'stream_truncate';

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
