<?php

namespace mageekguy\atoum\test\adapter;

use
	mageekguy\atoum\test\adapter
;

class storage implements \countable, \iteratorAggregate
{
	protected $adapters = null;

	public function __construct()
	{
		$this->reset();
	}

	public function count()
	{
		return sizeof($this->adapters);
	}

	public function add(adapter $adapter)
	{
		if ($this->contains($adapter) === false)
		{
			$this->adapters->attach($adapter);
		}

		return $this;
	}

	public function contains(adapter $adapter)
	{
		return $this->adapters->contains($adapter);
	}

	public function reset()
	{
		$this->adapters = new \splObjectStorage();

		return $this;
	}

	public function getIterator()
	{
		$adapters = array();

		foreach ($this->adapters as $instance)
		{
			$adapters[] = $instance;
		}

		return new \arrayIterator($adapters);
	}

	public function resetCalls()
	{
		foreach ($this->adapters as $adapter)
		{
			$adapter->resetCalls();
		}

		return $this;
	}
}
