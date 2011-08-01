<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\exceptions
;

class stream extends atoum\asserter
{
	protected $streamName = null;
	protected $streamController = null;

	public function setWith($stream)
	{
		$this->streamName = $stream;
		$this->streamController = mock\stream::get($stream);

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

	public function isUsedBy($functionName)
	{
		$this->streamIsSet();
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

?>
