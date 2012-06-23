<?php

namespace mageekguy\atoum\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\reports,
	mageekguy\atoum\report\writers
;

class file extends atoum\writer implements writers\realtime, writers\asynchronous
{
	protected $filename = null;

	private $handler = null;

	const defaultFileName = 'atoum.log';

	public function __construct($filename = null, atoum\adapter $adapter = null)
	{
		parent::__construct($adapter);

		$this->setFilename($filename ?: self::defaultFileName);
	}

	public function __destruct()
	{
		if($this->handler !== null)
		{
			$this->adapter->fclose($this->handler);
		}
	}

	public function write($something)
	{
		if($this->handler === null)
		{
			$dir = $this->adapter->dirname($this->filename);

			if($this->adapter->is_writable($dir))
			{
				$this->handler = $this->adapter->fopen($this->filename, 'w');
			}
		}

		$this->adapter->fwrite($this->handler, $something);

		return $this;
	}

	public function clear()
	{
		$this->adapter->ftruncate($this->handler, 0);

		return $this;
	}

	public function writeRealtimeReport(reports\realtime $report, $event)
	{
		return $this->write((string) $report);
	}

	public function writeAsynchronousReport(reports\asynchronous $report)
	{
		return $this->write((string) $report);
	}

	public function setFilename($filename)
	{
		if($this->handler === null)
		{
			$this->filename = $filename;
		}

		return $this;
	}

	public function getFilename()
	{
		return $this->filename;
	}
}
