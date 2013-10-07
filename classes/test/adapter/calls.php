<?php

namespace mageekguy\atoum\test\adapter;

use
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\adapter
;

class calls implements \countable, \arrayAccess, \iteratorAggregate
{
	protected $calls = array();
	protected $size = 0;
	protected $decorator = null;

	private static $callsNumber = 0;

	public function __construct()
	{
		$this->setDecorator();
	}

	public function __invoke()
	{
		return $this->calls;
	}

	public function __toString()
	{
		return $this->decorator->decorate($this);
	}

	public function count()
	{
		return $this->size;
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
		$this->size = 0;

		return $this;
	}

	public function setDecorator(adapter\calls\decorator $decorator = null)
	{
		$this->decorator = $decorator ?: new adapter\calls\decorator();

		return $this;
	}

	public function getDecorator()
	{
		return $this->decorator;
	}

	public function addCall(adapter\call $call)
	{
		$function = $call->getFunction();

		if ($function == '')
		{
			throw new exceptions\logic\invalidArgument('Function is undefined');
		}

		$this->calls[$function][++self::$callsNumber] = $call;
		$this->size++;

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

	public function getTimeline(adapter\call $call = null, $identical = false)
	{
		$timeline = array();

		foreach ($this as $innerCalls)
		{
			foreach ($innerCalls as $position => $call)
			{
				$timeline[$position] = $call;
			}
		}

		ksort($timeline, SORT_NUMERIC);

		return $timeline;
	}

	private static function normalizeFunction($mixed)
	{
		return ($mixed instanceof adapter\call ? $mixed->getFunction() : adapter\call::normalizeFunction($mixed));
	}
}