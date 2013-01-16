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

	public function __construct($stream)
	{
		parent::__construct($stream);

		$this->mode = static::defaultMode;

		$this->url_stat = array('mode' => $this->mode);
		$this->stream_open = true;
		$this->stream_close = true;
		$this->rename = true;
		$this->unlink = true;

		$pointer = & $this->pointer;
		$contents = & $this->contents;

		$this->stream_read = function($length) use (& $pointer, & $contents) {
				$read = '';

				if ($pointer < strlen($contents))
				{
					$read = substr($contents, $pointer, $length);

					$pointer += $length;
				}

				return $read;
			}
		;

		$this->stream_seek = function($offset, $whence = SEEK_SET) use (& $pointer, & $contents) {
			if ($offset < strlen($contents))
			{
				$pointer = $offset;
			}

			return true;
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

	public function getPointer()
	{
		return $this->pointer;
	}

	public function resetPointer()
	{
		$this->pointer = 0;

		return $this;
	}

	public function getContents()
	{
		return $this->contents;
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

		return $this;
	}
}
