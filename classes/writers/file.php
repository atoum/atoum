<?php

namespace mageekguy\atoum\writers;

use mageekguy\atoum;

class file extends atoum\writer
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
		return $this->flush($something);
	}

	public function flush($something)
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
