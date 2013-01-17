<?php

namespace mageekguy\atoum\mock\streams\file;

use
	mageekguy\atoum\mock\stream
;

class controller extends stream\controller
{
	const defaultMode = 33188;

	protected $pointer = 0;
	protected $contents = '';
	protected $mode = 0;
	protected $lock = null;

	public function __construct($stream)
	{
		parent::__construct($stream);

		$self = & $this;

		$this->mode = static::defaultMode;

		$this->url_stat = array('mode' => $this->mode);
		$this->stream_close = true;
		$this->rename = true;
		$this->unlink = true;

		$this->stream_open = function($path , $mode , $options , & $opened_path) use (& $self) {
			$self->seek(0);

			return true;
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
			return $self->lock($operation);
		};
	}

	public function setMode($mode)
	{
		$this->mode = 0100000 | octdec($mode);

		return parent::__set('url_stat', array('uid' => getmyuid(), 'mode' => $this->mode));
	}

	public function getMode()
	{
		return sprintf('%03o', $this->mode & 07777);
	}

	public function getLock()
	{
		return $this->lock;
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

	public function lock($lock)
	{
		switch ($lock)
		{
			case LOCK_UN:
				$this->lock = null;
				return true;

			case LOCK_SH:
			case LOCK_SH | LOCK_NB:
			case LOCK_EX:
			case LOCK_EX | LOCK_NB:
				if ($this->lock !== null)
				{
					return false;
				}
				else
				{
					$this->lock = $lock;

					return true;
				}
		}
	}

	public function read($length)
	{
		$data = '';

		if ($this->pointer >= 0 && $this->pointer < strlen($this->contents))
		{
			$data = substr($this->contents, $this->pointer, $length);

		}

		$this->pointer += $length;

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

		return $self->setContents($contents);
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

		$this->pointer = $offset;

		return ($this->pointer >= 0 && $this->pointer < strlen($this->contents));
	}

	public function eof()
	{
		return ($this->pointer > strlen($this->contents));
	}

	public function linkContentsTo(self $controller)
	{
		$this->contents = & $controller->contents;

		return $this;
	}

	public function linkModeTo(self $controller)
	{
		$this->mode = & $controller->mode;

		return $this;
	}

	public function linkLockTo(self $controller)
	{
		$this->lock = & $controller->lock;

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

	public function canNotBeRead()
	{
		return $this->setMode('0');
	}

	public function canBeRead()
	{
		return $this->setMode('444');
	}

	public function canNotBeWrited()
	{
		return $this->setMode('444');
	}

	public function canBeWrited()
	{
		return $this->setMode('644');
	}

	public function contains($contents)
	{
		$this->contents = $contents;
		$this->pointer = 0;

		return $this;
	}

	public function isEmpty()
	{
		return $this->contains('');
	}
}
