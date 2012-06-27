<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class stream extends atoum\asserter
{
	protected $streamName = null;
	protected $streamController = null;

	public function setWith($stream)
	{
		$this->streamName = $stream;
		$this->streamController = atoum\mock\stream::get($stream);

		return $this;
	}

	public function getStreamName()
	{
		return $this->streamName;
	}

	public function getStreamController()
	{
		return $this->streamController;
	}

	public function isRead($failMessage = null)
	{
		$calls = $this->streamIsSet()->streamController->getCalls();

		if (isset($calls['stream_read']) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('stream %s is not read'), $this->streamName));
		}

		return $this;
	}

	public function isWrited($failMessage = null)
	{
		$calls = $this->streamIsSet()->streamController->getCalls();

		if (isset($calls['stream_write']) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('stream %s is not writed'), $this->streamName));
		}

		return $this;
	}

	protected function streamIsSet()
	{
		if ($this->streamName === null)
		{
			throw new exceptions\logic('Stream is undefined');
		}

		return $this;
	}
}
