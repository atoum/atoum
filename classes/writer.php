<?php

namespace mageekguy\atoum;

abstract class writer implements adapter\aggregator
{
	protected $adapter = null;

	public function __construct(adapter $adapter = null)
	{
		$this->setAdapter($adapter ?: new adapter());
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
	public abstract function clear();
}
