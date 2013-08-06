<?php

namespace mageekguy\atoum;

abstract class writer
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

	public function reset()
	{
		return $this;
	}

	public abstract function write($string);
	public abstract function clear();
}
