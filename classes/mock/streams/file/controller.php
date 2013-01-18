<?php

namespace mageekguy\atoum\mock\streams\file;

use
	mageekguy\atoum\mock\stream
;

class controller extends stream\controller
{
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

		$self = & $this;

		$this->stream_close = true;
		$this->rename = true;
		$this->unlink = true;

		$this->stream_open = function($path , $mode , $options , & $opened_path) use (& $self) {
			$self->seek(0);

			return true;
		};

		$this->stat = function() use (& $self) {
			return $self->stat();
		};

		$this->url_stat = function($path, $flags) use (& $self) {
			return $self->stat();
		};

		$this->stream_tell = function() use (& $self) {
			return $self->tell();
		};

		$this->stream_read = function($length) use (& $self) {
			return $self->read($length);
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

		return true;
	}

	public function getContents()
	{
		return $this->contents;
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

	public function stat()
	{
		return $this->stats;
	}

	public function contains($contents)
	{
		$this->contents = $contents;
		$this->stats['size'] = ($contents == '' ? 0 : strlen($contents) + 1);
		$this->pointer = 0;

		return $this;
	}

	public function isEmpty()
	{
		return $this->contains('');
	}
}
