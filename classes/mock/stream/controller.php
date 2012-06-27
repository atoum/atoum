<?php

namespace mageekguy\atoum\mock\stream;

use
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\dependence,
	mageekguy\atoum\dependencies
;

class controller extends test\adapter
{
	protected $stream = '';

	public function __construct($stream, dependencies $dependencies = null)
	{
		parent::__construct($dependencies);

		$this->stream = (string) $stream;
	}

	public function __toString()
	{
		return $this->getStream();
	}

	public function __get($method)
	{
		$this->dependencies['invoker']['method'] = $method = strtolower(self::mapMethod($method));

		return parent::__get($method);
	}

	public function __set($method, $value)
	{
		switch ($method)
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
				$method = self::mapMethod($method);

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
		return parent::__isset(self::mapMethod($method));
	}

	public function setDependencies(dependencies $dependencies)
	{
		$dependencies['invoker'] = $dependencies['invoker'] ?: function($dependencies) { return new invoker($dependencies['method']()); };

		return parent::setDependencies($dependencies);
	}

	public function getStream()
	{
		return $this->stream;
	}

	public function getBasename()
	{
		return basename($this->stream);
	}

	public function invoke($method, array $arguments = array())
	{
		$method = self::mapMethod($method);

		if ($method === 'dir_rewinddir' && isset($this->{$method}) === true)
		{
			$this->resetCalls('dir_readdir');
		}

		return (isset($this->{$method}) === false ? null : parent::invoke($method, $arguments));
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
