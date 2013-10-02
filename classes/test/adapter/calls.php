<?php

namespace mageekguy\atoum\test\adapter;

class calls implements \countable, \arrayAccess
{
	protected $calls = array();

	private static $callsNumber = 0;

	public function count()
	{
		return sizeof($this->calls);
	}

	public function offsetSet($functionName = null, $call)
	{
		if ($functionName === null)
		{
			$functionName = $call->getFunction();
		}

		$this->calls[$functionName][++self::$callsNumber] = clone $call;

		return $this;
	}

	public function offsetGet($functionName)
	{
		return $this->calls[$functionName];
	}

	public function offsetUnset($functionName)
	{
	}

	public function offsetExists($functionName)
	{
	}
}
