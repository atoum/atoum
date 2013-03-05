<?php

namespace mageekguy\atoum\mock\streams\file;

use
	mageekguy\atoum\exceptions,
	mageekguy\atoum\mock\stream
;

class controller extends stream\controller
{
	protected $exists = true;
	protected $read = false;
	protected $write = false;
	protected $eof = false;
	protected $pointer = 0;
	protected $offset = null;
	protected $append = false;
	protected $contents = '';
	protected $stats = array();

	public function __construct($path)
	{
		parent::__construct($path);

		$this->stats = array(
			'dev' => 0,
			'ino' => 0,
			'mode' => 0,
			'nlink' => 0,
			'uid' => getmyuid(),
			'gid' => getmygid(),
			'rdev' => 0,
			'size' => 0,
			'atime' => 507769200,
			'mtime' => 507769200,
			'ctime' => 507769200,
			'blksize' => 0,
			'blocks' => 0
		);

		$this->stats[0] = & $this->stats['dev'];
		$this->stats[1] = & $this->stats['ino'];
		$this->stats[2] = & $this->stats['mode'];
		$this->stats[3] = & $this->stats['nlink'];
		$this->stats[4] = & $this->stats['uid'];
		$this->stats[5] = & $this->stats['gid'];
		$this->stats[6] = & $this->stats['rdev'];
		$this->stats[7] = & $this->stats['size'];
		$this->stats[8] = & $this->stats['atime'];
		$this->stats[9] = & $this->stats['mtime'];
		$this->stats[10] = & $this->stats['ctime'];
		$this->stats[11] = & $this->stats['blksize'];
		$this->stats[12] = & $this->stats['blocks'];

		$this->setMode('644');
	}

	public function __set($method, $value)
	{
		switch ($method = static::mapMethod($method))
		{
			case 'mkdir':
			case 'rmdir':
			case 'dir_closedir':
			case 'dir_opendir':
			case 'dir_readdir':
			case 'dir_rewinddir':
				throw new exceptions\logic\invalidArgument('Unable to override streamWrapper::' . $method . '() for file');

			default:
				return parent::__set($method, $value);
		}
	}

	public function duplicate()
	{
		$controller = parent::duplicate();

		$controller->contents = & $this->contents;
		$controller->stats = & $this->stats;
		$controller->exists = & $this->exists;

		return $controller;
	}

	public function setMode($mode)
	{
		$this->stats['mode'] = 0100000 | octdec($mode);

		return $this;
	}

	public function getMode()
	{
		return (int) sprintf('%03o', $this->stats['mode'] & 07777);
	}

	public function getPointer()
	{
		return $this->pointer;
	}

	public function setContents($contents)
	{
		$this->contents = $contents;
		$this->stats['size'] = strlen($this->contents);

		return $this->clearStat();
	}

	public function getContents()
	{
		return $this->contents;
	}

	public function exists()
	{
		$this->exists = true;

		return $this;
	}

	public function notExists()
	{
		$this->exists = false;

		return $this->clearStat();
	}

	public function isNotReadable()
	{
		return $this->removePermissions(0444);
	}

	public function isReadable()
	{
		return $this->addPermission(0444);
	}

	public function isNotWritable()
	{
		return $this->removePermissions(0222);
	}

	public function isWritable()
	{
		return $this->addPermission(0222);
	}

	public function isNotExecutable()
	{
		return $this->removePermissions(0111);
	}

	public function isExecutable()
	{
		return $this->addPermission(0111);
	}

	public function contains($contents)
	{
		return $this
			->setContents($contents)
			->setPointer(0)
		;
	}

	public function isEmpty()
	{
		return $this->contains('');
	}

	public function stream_open($path, $mode, $options, & $openedPath = null)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			$this->offset = null;
			$this->append = false;

			$isOpened = false;

			$reportErrors = ($options & STREAM_REPORT_ERRORS) == STREAM_REPORT_ERRORS;

			if (self::checkOpenMode($mode) === false)
			{
				if ($reportErrors === true)
				{
					trigger_error('Operation timed out', E_USER_WARNING);
				}
			}
			else
			{
				$this->setOpenMode($mode);

				switch (true)
				{
					case $this->read === true && $this->write === false:
						$isOpened = $this->checkIfReadable();
						break;

					case $this->read === false && $this->write === true:
						$isOpened = $this->checkIfWritable();
						break;

					default:
						$isOpened = $this->checkIfReadable() && $this->checkIfWritable();
				}

				if ($isOpened === false)
				{
					if ($reportErrors === true)
					{
						trigger_error('Permission denied', E_USER_WARNING);
					}
				}
				else
				{
					switch (self::getRawOpenMode($mode))
					{
						case 'w':
							$this->exists = true;
							$this->truncate(0);
							$this->seek(0);
							break;

						case 'r':
							$isOpened = $this->exists;

							if ($isOpened === true)
							{
								$this->seek(0);
							}
							else if ($reportErrors === true)
							{
								trigger_error('No such file or directory', E_USER_WARNING);
							}
							break;

						case 'c':
							$this->exists = true;
							$this->seek(0);
							break;

						case 'x':
							if ($this->exists === false)
							{
								$this->seek(0);
							}
							else
							{
								$isOpened = false;

								if ($reportErrors === true)
								{
									trigger_error('File exists', E_USER_WARNING);
								}
							}
							break;

						case 'a':
							$this->exists = true;

							if ($this->read === true)
							{
								$this->seek(0);
							}
							else
							{
								$this->seek(0, SEEK_END);
								$this->offset = $this->pointer;
							}

							$this->append = true;
							break;
					}
				}
			}

			$openedPath = null;

			if ($isOpened === true && $options & STREAM_USE_PATH)
			{
				$openedPath = $this->getPath();
			}

			return $isOpened;
		}
	}

	public function stream_seek($offset, $whence = SEEK_SET)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			return $this->seek($offset, $whence);
		}
	}

	public function stream_eof()
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, array());

			return $this->eof;
		}
	}

	public function stream_tell()
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, array());
		}
		else
		{
			$this->addCall(__FUNCTION__, array());

			return ($this->offset === null ? $this->pointer : $this->pointer - $this->offset);
		}
	}

	public function stream_read($count)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			$data = '';

			$this->eof = ($this->pointer < 0 || $this->pointer >= $this->stats['size']);

			if ($this->read === true && $this->pointer >= 0 && $this->eof === false)
			{
				$data = substr($this->contents, $this->pointer, $count) ?: '';

				$this->movePointer(strlen($data) ?: $count);
			}

			return $data;
		}
	}

	public function stream_write($data)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			$bytesWrited = 0;

			if ($this->write === true)
			{
				$contents = $this->getContents();

				if ($this->append === true)
				{
					if ($contents !== '')
					{
						$contents .= PHP_EOL;
						$this->movePointer(1);
					}

					$this->append = false;
				}

				$this
					->setContents($contents . $data)
					->movePointer($bytesWrited = strlen($data))
				;
			}

			return $bytesWrited;
		}
	}

	public function stream_flush()
	{
		return true;
	}

	public function stream_metadata($path, $option, $value)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			switch ($option)
			{
				case STREAM_META_TOUCH:
					return true;

				case STREAM_META_OWNER_NAME:
					return true;

				case STREAM_META_OWNER:
					return true;

				case STREAM_META_GROUP_NAME:
					return true;

				case STREAM_META_GROUP:
					return true;

				case STREAM_META_ACCESS:
					$this->setMode($value);
					return true;

				default:
					return false;
			}
		}
	}

	public function stream_stat()
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, array());
		}
		else
		{
			$this->addCall(__FUNCTION__, array());

			return $this->stat();
		}
	}

	public function stream_truncate($newSize)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			return $this->truncate($newSize);
		}
	}

	public function stream_lock($mode)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			return true;
		}
	}

	public function stream_close()
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, array());
		}
		else
		{
			$this->addCall(__FUNCTION__, array());

			return true;
		}
	}

	public function url_stat($path, $flags)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			return $this->stat();
		}
	}

	public function unlink($path)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			if ($this->exists === false || $this->checkIfWritable() === false)
			{
				return false;
			}
			else
			{
				$this->exists = false;

				return true;
			}
		}
	}

	public function rename($from, $to)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return parent::invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			$this->setPath($to);

			return true;
		}
	}

	public function mkdir($path, $mode, $options)
	{
		return false;
	}

	public function dir_opendir($path, $options)
	{
		return false;
	}

	public function dir_readdir()
	{
		return false;
	}

	public function dir_rewinddir()
	{
		return false;
	}

	public function dir_closedir()
	{
		return false;
	}

	public function rmdir($path, $options)
	{
		return false;
	}

	public function invoke($method, array $arguments = array())
	{
		return call_user_func_array(array($this, static::mapMethod($method)), $arguments);
	}

	protected function stat()
	{
		return ($this->exists === false ? false : $this->stats);
	}

	protected function truncate($newSize)
	{
		$this->setContents(str_pad(substr($this->contents, 0, $newSize), $newSize, "\0"));

		return true;
	}

	protected function seek($offset, $whence = SEEK_SET)
	{
		switch ($whence)
		{
			case SEEK_CUR:
				$offset = $this->pointer + $offset;
				break;

			case SEEK_END:
				$offset = strlen($this->getContents()) + $offset;
		}

		if ($this->offset !== null && $offset < $this->offset)
		{
			$offset = $this->offset;
		}

		$this->setPointer($offset);

		return true;
	}

	protected function addPermission($permissions)
	{
		$this->stats['mode'] = $this->stats['mode'] | $permissions;

		return $this->clearStat();
	}

	protected function removePermissions($permissions)
	{
		$this->stats['mode'] = $this->stats['mode'] & ~ $permissions;

		return $this->clearStat();
	}

	protected function setOpenMode($mode)
	{
		$this->read = false;
		$this->write = false;

		switch (str_replace(array('b', 't'), '', $mode))
		{
			case 'r':
			case 'x':
				$this->read = true;
				break;

			case 'w':
			case 'a':
			case 'c':
				$this->write = true;
				break;

			case 'r+':
			case 'x+':
			case 'w+':
			case 'a+':
			case 'c+':
				$this->read = $this->write = true;
		}

		return $this;
	}

	protected function checkIfReadable()
	{
		return $this->checkPermission(0400, 0040, 0004);
	}

	protected function checkIfWritable()
	{
		return $this->checkPermission(0200, 0020, 0002);
	}

	protected function checkPermission($user, $group, $other)
	{
		$permissions = $this->stats['mode'] & 07777;

		switch (true)
		{
			case getmyuid() === $this->stats['uid']:
				return ($permissions & $user) > 0;

			case getmygid() === $this->stats['gid']:
				return ($permissions & $group) > 0;

			default:
				return ($permissions & $other) > 0;
		}
	}

	protected function setPointer($pointer)
	{
		$this->pointer = $pointer;
		$this->eof = false;

		return $this;
	}

	protected function movePointer($offset)
	{
		return $this->setPointer($this->pointer + $offset);
	}

	protected function clearStat()
	{
		clearstatcache(false, $this->getPath());

		return $this;
	}

	protected static function getRawOpenMode($mode)
	{
		return str_replace(array('b', 't', '+'), '', $mode);
	}

	protected static function checkOpenMode($mode)
	{
		switch (self::getRawOpenMode($mode))
		{
			case 'r':
			case 'w':
			case 'a':
			case 'x':
			case 'c':
				return true;

			default:
				return false;
		}
	}
}
