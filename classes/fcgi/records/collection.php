<?php

namespace mageekguy\atoum\fcgi\records;

use
	mageekguy\atoum\fcgi
;

class collection implements \iterator, \countable, \arrayAccess
{
	protected $requestId = null;
	protected $records = array();

	public function __construct(array $records = array())
	{
		foreach ($records as $record)
		{
			$this->setRecord($record);
		}
	}

	public function rewind()
	{
		reset($this->records);

		return $this;
	}

	public function key()
	{
		return key($this->records);
	}

	public function current()
	{
		return (current($this->records) ?: null);
	}

	public function valid()
	{
		return (key($this->records) !== null);
	}

	public function next()
	{
		next($this->records);

		return $this;
	}

	public function offsetSet($mixed, $record)
	{
		return $this->setRecord($record, $mixed);
	}

	public function offsetGet($mixed)
	{
		return $this->getRecord($mixed);
	}

	public function offsetUnset($mixed)
	{
		return $this->unsetRecord($mixed);
	}

	public function offsetExists($mixed)
	{
		return $this->recordIsSet($mixed);
	}

	public function getRequestId()
	{
		return $this->requestId;
	}

	public function setRecord(fcgi\record $record, $mixed = null)
	{
		if ($this->requestId !== null && $this->requestId !== $record->getRequestId())
		{
			throw new collection\exception('Unable to set record with request ID \'' . $record->getRequestId() . '\' in a collection with request ID \'' . $this->requestId . '\'');
		}

		if ($mixed === null)
		{
			$this->records[] = $record;
		}
		else
		{
			$this->records[$mixed] = $record;
		}

		if (sizeof($this) === 1)
		{
			$this->requestId = $record->getRequestId();
		}

		return $this;
	}

	public function getRecord($mixed)
	{
		return (isset($this->records[$mixed]) === false ? null : $this->records[$mixed]);
	}

	public function unsetRecord($mixed)
	{
		if (isset($this->records[$mixed]) === true)
		{
			unset($this->records[$mixed]);
		}

		if (sizeof($this) === 0)
		{
			$this->requestId = null;
		}

		return $this;
	}

	public function recordIsSet($mixed)
	{
		return isset($this->records[$mixed]);
	}

	public function count()
	{
		return sizeof($this->records);
	}

	public function getStreamData()
	{
		$streamData = '';

		foreach ($this->records as $record)
		{
			$streamData .= $record->getStreamData();
		}

		return $streamData;
	}
}
