<?php
namespace mageekguy\atoum\writers;
use mageekguy\atoum;
class file extends atoum\writer
{
	protected $filename = null;
	private $handler = null;

	public function __construct($filename = null, atoum\adapter $adapter = null)
	{
		parent::__construct($adapter);
		if($filename === null)
		{
			$filename = 'atoum.log';
		}
		$this->setFilename($filename);
		$this->handler = $this->adapter->fopen($this->filename, 'w');
	}

	public function write($something)
	{
		return $this->flush($something);
	}

	public function flush($something)
	{
		$this->adapter->fwrite($this->handler, $something);
		return $this;
	}

	public function setFilename($filename)
	{
		$this->filename = $filename;
		return $this;
	}

	public function getFilename()
	{
		return $this->filename;
	}

	public function __destruct()
	{
		$this->adapter->fclose($this->handler);
	}
}
?>
