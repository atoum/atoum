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

		if($filename === null)
		{
			$filename = self::defaultFileName;
		}

		$this->setFilename($filename);
	}

	public function write($something)
	{
		if($this->adapter->is_null($this->handler))
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

	public function realtimeWrite(reports\realtime $report)
	{
		return $this->write((string) $report);
	}

	public function asynchronousWrite(reports\asynchronous $report)
	{
		return $this->write((string) $report);
	}

	public function setFilename($filename)
	{
		if($this->adapter->is_null($this->handler))
		{
			$this->filename = $filename;
		}
		return $this;
	}

	public function getFilename()
	{
		return $this->filename;
	}

	public function __destruct()
	{
		if(!$this->adapter->is_null($this->handler))
		{
			$this->adapter->fclose($this->handler);
		}
	}
}

?>
