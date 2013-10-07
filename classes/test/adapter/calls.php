<?php

namespace mageekguy\atoum\test\adapter;

use
	mageekguy\atoum\test\adapter
;

class calls implements \countable, \arrayAccess, \iteratorAggregate
{
	protected $calls = array();

	private static $callsNumber = 0;

	public function __invoke()
	{
		return $this->calls;
	}

	public function count()
	{
		return sizeof($this->calls);
	}

	public function offsetSet($functionName = null, $call)
	{
		return $this->addCall($call);
	}

	public function offsetGet($mixed)
	{
		$mixed = static::normalizeFunction($mixed);

		return (isset($this->calls[$mixed]) === false ? array() : $this->calls[$mixed]);
	}

	public function offsetUnset($mixed)
	{
		$mixed = static::normalizeFunction($mixed);

		if (isset($this->calls[$mixed]) === true)
		{
			unset($this->calls[$mixed]);
		}

		return $this;
	}

	public function offsetExists($mixed)
	{
		return (isset($this->calls[static::normalizeFunction($mixed)]) === true);
	}

	public function getIterator()
	{
		return new \arrayIterator($this());
	}

	public function reset()
	{
		$this->calls = array();

		return $this;
	}

	public function addCall(adapter\call $call)
	{
		$this->calls[$call->getFunction()][++self::$callsNumber] = $call;

		return $this;
	}

	public function getEqualTo(adapter\call $call)
	{
		$calls = array();

		if (isset($this[$call]) === true)
		{
			$calls = array_filter($this[$call], function($innerCall) use ($call) { return $call->isEqualTo($innerCall); });
		}

		return $calls;
	}

	public function getIdenticalTo(adapter\call $call)
	{
		$calls = array();

		if (isset($this[$call]) === true)
		{
			$calls = array_filter($this[$call], function($innerCall) use ($call) { return $call->isIdenticalTo($innerCall); });
		}

		return $calls;
	}

	public function get(adapter\call $call, $identical = false)
	{
		return ($identical === false ? $this->getEqualTo($call) : $this->getIdenticalTo($call));
	}

	public function getFirstEqualTo(adapter\call $call)
	{
		if (isset($this[$call]) === true)
		{
			foreach ($this[$call] as $position => $innerCall)
			{
				if ($call->isEqualTo($innerCall) === true)
				{
					return array($position => $innerCall);
				}
			}
		}

		return null;
	}

	public function getFirstIdenticalTo(adapter\call $call)
	{
		if (isset($this[$call]) === true)
		{
			foreach ($this[$call] as $position => $innerCall)
			{
				if ($call->isIdenticalTo($innerCall) === true)
				{
					return array($position => $innerCall);
				}
			}
		}

		return null;
	}

	public function getFirst(adapter\call $call, $identical = false)
	{
		return ($identical === false ? $this->getFirstEqualTo($call) : $this->getFirstIdenticalTo($call));
	}

	public function getLastEqualTo(adapter\call $call)
	{
		if (isset($this[$call]) === true)
		{
			foreach (array_reverse($this[$call], true) as $position => $innerCall)
			{
				if ($call->isEqualTo($innerCall) === true)
				{
					return array($position => $innerCall);
				}
			}
		}

		return null;
	}

	public function getLastIdenticalTo(adapter\call $call)
	{
		if (isset($this[$call]) === true)
		{
			foreach (array_reverse($this[$call], true) as $position => $innerCall)
			{
				if ($call->isIdenticalTo($innerCall) === true)
				{
					return array($position => $innerCall);
				}
			}
		}

		return null;
	}

	public function getLast(adapter\call $call, $identical = false)
	{
		return ($identical === false ? $this->getLastEqualTo($call) : $this->getLastIdenticalTo($call));
	}

	private static function normalizeFunction($mixed)
	{
		return ($mixed instanceof adapter\call ? $mixed->getFunction() : adapter\call::normalizeFunction($mixed));
	}
}
