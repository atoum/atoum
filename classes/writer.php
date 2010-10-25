<?php

namespace mageekguy\atoum;

abstract class writer
{
	protected $adapter = null;

	public function __construct(adapter $adapter = null)
	{
		if ($adapter === null)
		{
			$adapter = new adapter();
		}

		$this->setAdapter($adapter);
	}

	public function setAdapter(adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public abstract function write($string);
}

?>
