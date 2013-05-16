<?php

namespace mageekguy\atoum;

abstract class reader
{
	protected $adapter = null;

	public function __construct(adapter $adapter = null)
	{
		$this->setAdapter($adapter);
	}

	public function setAdapter(adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public abstract function read($length = null);
}
