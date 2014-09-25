<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions
;

class stream extends atoum\asserter
{
	protected $streamController = null;

	public function setWith($stream)
	{
		parent::setWith($stream);

		$this->streamController = atoum\mock\stream::get($stream);

		return $this;
	}

	public function getStreamController()
	{
		return $this->streamController;
	}

	public function isRead($failMessage = null)
	{
		if (sizeof($this->streamIsSet()->streamController->getCalls(new test\adapter\call('stream_read'))) > 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('stream %s is not read', $this->streamController));
		}

		return $this;
	}

	public function isWrited($failMessage = null)
	{
		return $this->isWritten($failMessage);
	}

	public function isWritten($failMessage = null)
	{
		if (sizeof($this->streamIsSet()->streamController->getCalls(new test\adapter\call('stream_write'))) > 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('stream %s is not written', $this->streamController));
		}

		return $this;
	}

	protected function streamIsSet()
	{
		if ($this->streamController === null)
		{
			throw new exceptions\logic('Stream is undefined');
		}

		return $this;
	}
}
