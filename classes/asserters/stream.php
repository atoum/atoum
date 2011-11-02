<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

/**
 * @method  mageekguy\atoum\asserters\adapter               adapter()
 * @method  mageekguy\atoum\asserters\afterDestructionOf    afterDestructionOf()
 * @method  mageekguy\atoum\asserters\boolean               boolean()
 * @method  mageekguy\atoum\asserters\castToString          castToString()
 * @method  mageekguy\atoum\asserters\dateTime              dateTime()
 * @method  mageekguy\atoum\asserters\error                 error()
 * @method  mageekguy\atoum\asserters\exception             exception()
 * @method  mageekguy\atoum\asserters\float                 float()
 * @method  mageekguy\atoum\asserters\hash                  hash()
 * @method  mageekguy\atoum\asserters\integer               integer()
 * @method  mageekguy\atoum\asserters\mock                  mock()
 * @method  mageekguy\atoum\asserters\mysqlDateTime         mysqlDateTime()
 * @method  mageekguy\atoum\asserters\object                object()
 * @method  mageekguy\atoum\asserters\output                output()
 * @method  mageekguy\atoum\asserters\phpArray              phpArray()
 * @method  mageekguy\atoum\asserters\phpClass              phpClass()
 * @method  mageekguy\atoum\asserters\sizeOf                sizeOf()
 * @method  mageekguy\atoum\asserters\stream                stream()
 * @method  mageekguy\atoum\asserters\string                string()
 * @method  mageekguy\atoum\asserters\testedClass           testedClass()
 * @method  mageekguy\atoum\asserters\variable              variable()
 */
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

?>
