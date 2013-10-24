<?php

namespace mageekguy\atoum\mock\streams\fs\file;

use
	mageekguy\atoum\exceptions,
	mageekguy\atoum\mock\streams\fs
;

class controller extends fs\controller
{
	protected $exists = true;
	protected $read = false;
	protected $write = false;
	protected $eof = false;
	protected $pointer = 0;
	protected $offset = null;
	protected $append = false;
	protected $contents = '';

	public function __construct($path)
	{
		parent::__construct($path);

		$this->setPermissions('644');
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

		return $controller;
	}

	public function setPermissions($permissions)
	{
		return parent::setPermissions(0100000 | octdec($permissions));
	}

	public function getPointer()
	{
		return $this->pointer;
	}

	public function setContents($contents)
	{
		$this->contents = $contents;

		return $this->setStat('size', strlen($this->contents));
	}

	public function getContents()
	{
		return $this->contents;
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
			return $this->invoke(__FUNCTION__, func_get_args());
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

			if ($isOpened === true && ($options & STREAM_USE_PATH))
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
			return $this->invoke(__FUNCTION__, func_get_args());
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
			return $this->invoke(__FUNCTION__, func_get_args());
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
			return $this->invoke(__FUNCTION__, array());
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
			return $this->invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			$data = '';

			$this->eof = ($this->pointer < 0 || $this->pointer >= $this->stat['size']);

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
			return $this->invoke(__FUNCTION__, func_get_args());
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
			return $this->invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			switch ($option)
			{
				case STREAM_META_TOUCH:
				case STREAM_META_OWNER_NAME:
				case STREAM_META_OWNER:
				case STREAM_META_GROUP_NAME:
				case STREAM_META_GROUP:
					return true;

				case STREAM_META_ACCESS:
					$this->setPermissions($value);
					return true;

				default:
					return false;
			}
		}
	}

	public function stream_truncate($newSize)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return $this->invoke(__FUNCTION__, func_get_args());
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
			return $this->invoke(__FUNCTION__, func_get_args());
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
			return $this->invoke(__FUNCTION__, array());
		}
		else
		{
			$this->addCall(__FUNCTION__, array());

			return true;
		}
	}

	public function unlink($path)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return $this->invoke(__FUNCTION__, func_get_args());
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
			return $this->invoke(__FUNCTION__, func_get_args());
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
