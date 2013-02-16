<?php

namespace mageekguy\atoum\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\reports,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\report\writers
;

class file extends atoum\writer implements writers\realtime, writers\asynchronous
{
	protected $filename = null;

	private $resource = null;

	const defaultFileName = 'atoum.log';

	public function __construct($filename = null, atoum\adapter $adapter = null)
	{
		parent::__construct($adapter);

		$this->setFilename($filename ?: self::defaultFileName);
	}

	public function __destruct()
	{
		$this->closeFile();
	}

	public function write($something)
	{
		if (strlen($something) != $this->openFile()->adapter->fwrite($this->resource, $something))
		{
			throw  new exceptions\runtime('Unable to write in file \'' . $this->filename . '\'');
		}

		$this->adapter->fflush($this->resource);

		return $this;
	}

	public function clear()
	{
		if ($this->openFile()->adapter->ftruncate($this->resource, 0) === false)
		{
			throw  new exceptions\runtime('Unable to truncate file \'' . $this->filename . '\'');
		}

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
		$this->closeFile()->filename = $filename;

		return $this;
	}

	public function getFilename()
	{
		return $this->filename;
	}

	private function openFile()
	{
		if ($this->resource === null)
		{
			$this->resource = $this->adapter->fopen($this->filename, 'w') ?: null;

			if ($this->resource === null)
			{
				throw  new exceptions\runtime('Unable to open file \'' . $this->filename . '\'');
			}
		}

		return $this;
	}

	private function closeFile()
	{
		if ($this->resource !== null)
		{
			$this->adapter->fclose($this->resource);

			$this->resource = null;
		}

		return $this;
	}
}
