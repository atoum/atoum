<?php

namespace mageekguy\atoum\mock\streams\file;

use
	mageekguy\atoum\mock\stream
;

class controller extends stream\controller
{
	protected $exists = true;
	protected $read = false;
	protected $write = false;
	protected $eof = false;
	protected $pointer = 0;
	protected $contents = '';
	protected $stats = array();

	public function __construct($stream)
	{
		parent::__construct($stream);

		$this->stats = array(
			'dev' => 0,
			'ino' => 0,
			'mode' => 0,
			'nlink' => 0,
			'uid' => getmyuid(),
			'gid' => getmygid(),
			'rdev' => 0,
			'size' => 0,
			'atime' => time(),
			'mtime' => time(),
			'ctime' => time(),
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

		$self = & $this;

		$this->stream_close = true;
		$this->unlink = true;

		$this->stream_open = function($path , $mode , $options , & $openedPath = null) use (& $self) {
			return $self->open($mode, $options, $openedPath);
		};

		$this->stream_stat = function() use (& $self) {
			return $self->stat();
		};

		$this->url_stat = function($path, $flags) use (& $self) {
			return $self->stat();
		};

		$this->stream_metadata = function($path, $option, $var) use (& $self) {
			return $self->metadata($option, $var);
		};

		$this->stream_tell = function() use (& $self) {
			return $self->tell();
		};

		$this->stream_read = function($length) use (& $self) {
			return $self->read($length);
		};

		$this->stream_write = function($data) use (& $self) {
			return $self->write($data);
		};

		$this->stream_seek = function($offset, $whence = SEEK_SET) use (& $self) {
			return $self->seek($offset, $whence);
		};

		$this->stream_eof = function() use (& $self) {
			return $self->eof();
		};

		$this->stream_truncate = function($newSize) use (& $self) {
			return $self->truncate($newSize);
		};

		$this->stream_lock = function($operation) use (& $self) {
			return true;
		};

		$this->rename = function($from, $to) use (& $self) {
			return $self->setStream($to);
		};

		$this->setMode('644');
	}

	public function linkContentsTo(self $controller)
	{
		$this->contents = & $controller->contents;

		return $this;
	}

	public function linkStatsTo(self $controller)
	{
		$this->stats = & $controller->stats;

		return $this;
	}

	public function setMode($mode)
	{
		$this->stats['mode'] = 0100000 | octdec($mode);

		return $this;
	}

	public function getMode()
	{
		return sprintf('%03o', $this->stats['mode'] & 07777);
	}

	public function getPointer()
	{
		return $this->pointer;
	}

	public function setContents($contents)
	{
		$this->contents = $contents;
		$this->stats['size'] = ($contents == '' ? 0 : strlen($contents) + 1);

		return true;
	}

	public function getContents()
	{
		return $this->contents;
	}

	public function open($mode, $options, & $openedPath = null)
	{
		$isOpened = false;

		$reportErrors = ($options & STREAM_REPORT_ERRORS) == STREAM_REPORT_ERRORS;

		if (self::checkMode($mode) === false)
		{
			if ($reportErrors === true)
			{
			}
		}
		else
		{
			$this->setOpenMode($mode);

			if ($this->read === true)
			{
				$isOpened = $this->checkIfReadable();

				if ($reportErrors === true && $isOpened === false)
				{
				}
			}

			if ($this->write === true)
			{
				$isOpened = $this->checkIfWritable();

				if ($reportErrors === true && $isOpened === false)
				{
				}
			}

			if ($isOpened === true)
			{
				switch (self::getRawOpenMode($mode))
				{
					case 'w':
						$this->truncate(0);
						break;

					case 'r':
						$this->seek(0);
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
							}
						}
						break;

					case 'a':
						$this->seek(0, SEEK_END);
						break;
				}
			}
		}

		return $isOpened;
	}

	public function read($length)
	{
		$data = '';

		$contentsLength = strlen($this->contents);

		if ($this->pointer >= 0 && $this->pointer < $contentsLength)
		{
			$data = substr($this->contents, $this->pointer, $length);
		}

		$this->pointer += $length;

		if ($this->pointer >= $contentsLength)
		{
			$this->eof = true;
			$this->pointer = $contentsLength;
		}

		return $data;
	}

	public function write($data)
	{
		$bytesWrited = 0;

		if ($this->write === true)
		{
			$this->contents .= $data;
			$bytesWrited = strlen($data);
		}

		return $bytesWrited;
	}

	public function truncate($newSize)
	{
		$contents = $this->contents;

		if ($newSize < strlen($this->contents))
		{
			$contents = substr($contents, 0, $newSize);
		}
		else
		{
			$contents = str_pad($contents, $newSize, "\0");
		}

		return $this->setContents($contents);
	}

	public function seek($offset, $whence = SEEK_SET)
	{
		switch ($whence)
		{
			case SEEK_CUR:
				$offset = $this->pointer + $offset;
				break;

			case SEEK_END:
				$offset = strlen($this->contents) + $offset;
				break;
		}

		$this->eof = false;

		if ($this->pointer === $offset)
		{
			return false;
		}
		else
		{
			$this->pointer = $offset;

			return true;
		}
	}

	public function tell()
	{
		return $this->pointer;
	}

	public function eof()
	{
		return $this->eof;
	}

	public function stat()
	{
		return ($this->exists === false ? false : $this->stats);
	}

	public function metadata($option, $value)
	{
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

	public function exists()
	{
		$this->exists = true;

		return $this;
	}

	public function notExists()
	{
		$this->exists = false;

		return $this;
	}

	public function canNotBeOpened()
	{
		return parent::__set('fopen', false);
	}

	public function canBeOpened()
	{
		return parent::__set('fopen', true);
	}

	public function isNotReadable()
	{
		return $this->setMode('0');
	}

	public function isReadable()
	{
		return $this->setMode('444');
	}

	public function isNotWritable()
	{
		return $this->setMode('444');
	}

	public function isWritable()
	{
		return $this->setMode('644');
	}

	public function isExecutable()
	{
		return $this->setMode('744');
	}

	public function isNotExecutable()
	{
		return $this->setMode('644');
	}

	public function contains($contents)
	{
		$this->setContents($contents);
		$this->pointer = 0;

		return $this;
	}

	public function isEmpty()
	{
		return $this->contains('');
	}

	public function setStream($stream)
	{
		parent::setStream($stream);

		return true;
	}

	private function setOpenMode($mode)
	{
		$this->read = false;
		$this->write = false;

		switch (rtrim($mode, 'bt'))
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

	private function getPermissions()
	{
	}

	private function checkIfReadable()
	{
		return $this->checkPermission(0400, 0040, 0004);
	}

	private function checkIfWritable()
	{
		return $this->checkPermission(0200, 0020, 0002);
	}

	private function checkPermission($user, $group, $other)
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

	private static function checkMode($mode)
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

	private static function getRawOpenMode($mode)
	{
		return rtrim($mode, 'bt+');
	}
}
