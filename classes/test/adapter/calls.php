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

	public function offsetSet($functionName = null, $call = null)
	{
		if ($functionName !== null)
		{
			$call->setFunction($functionName);
		}

		return $this->addCall($call);
	}

	public function offsetGet($mixed)
	{
		return $this->getEqualTo(self::buildCall($mixed));
	}

	public function offsetUnset($mixed)
	{
		$key = self::getKey(self::buildCall($mixed));

		if (isset($this->calls[$key]) === true)
		{
			$this->size -= sizeof($this->calls[$key]);

			unset($this->calls[$key]);
		}

		return $this;
	}

	public function offsetExists($mixed)
	{
		return (isset($this->calls[self::getKey(self::buildCall($mixed))]) === true);
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
		return $this->setCall($call);
	}

	public function removeCall(adapter\call $call, $position)
	{
		$function = $call->getFunction();

		if ($function == '')
		{
			throw new exceptions\logic\invalidArgument('Function is undefined');
		}

		$key = self::getKey($call);

		if (isset($this->calls[$key][$position]) === true)
		{
			unset($this->calls[$key][$position]);
			$this->size--;
		}

		return $this;
	}

	public function toArray(adapter\call $call = null)
	{
		if ($call === null)
		{
			$calls = $this->getTimeline();
		}
		else
		{
			$calls = $this->getEqualTo($call)->toArray();
		}

		return $calls;
	}

	public function getEqualTo(adapter\call $call)
	{
		$innerCalls = $this->getCalls($call);

		if ($call->getArguments() !== null)
		{
			$innerCalls = array_filter($innerCalls, function($innerCall) use ($call) { return $call->isEqualTo($innerCall); });
		}

		return self::buildCallsForCall($call, $innerCalls);
	}

	public function getIdenticalTo(adapter\call $call)
	{
		$innerCalls = $this->getCalls($call);

		if ($call->getArguments() !== null)
		{
			$innerCalls = array_filter($innerCalls, function($innerCall) use ($call) { return $call->isIdenticalTo($innerCall); });
		}

		return self::buildCallsForCall($call, $innerCalls);
	}

	public function getPreviousEqualTo(adapter\call $call, $position)
	{
		$calls = new static();

		foreach ($this->getEqualTo($call)->toArray() as $innerPosition => $innerCall)
		{
			if ($innerPosition < $position)
			{
				$calls->setCall($innerCall, $innerPosition);
			}
		}

		return $calls;
	}

	public function getPreviousIdenticalTo(adapter\call $call, $position)
	{
		$calls = new static();

		foreach ($this->getIdenticalTo($call)->toArray() as $innerPosition => $innerCall)
		{
			if ($innerPosition < $position)
			{
				$calls->setCall($innerCall, $innerPosition);
			}
		}

		return $calls;
	}

	public function getPrevious(adapter\call $call, $position, $identical = false)
	{
		return ($identical === false ? $this->getPreviousEqualTo($call, $position) : $this->getPreviousIdenticalTo($call, $position));
	}

	public function hasPreviousEqualTo(adapter\call $call, $position)
	{
		foreach ($this->getEqualTo($call)->toArray() as $innerPosition => $innerCall)
		{
			if ($innerPosition < $position)
			{
				return true;
			}
		}

		return false;
	}

	public function hasPreviousIdenticalTo(adapter\call $call, $position)
	{
		foreach ($this->getIdenticalTo($call)->toArray() as $innerPosition => $innerCall)
		{
			if ($innerPosition < $position)
			{
				return true;
			}
		}

		return false;
	}

	public function hasPrevious(adapter\call $call, $position, $identical = false)
	{
		return ($identical === false ? $this->hasPreviousEqualTo($call, $position) : $this->hasPreviousIdenticalTo($call, $position));
	}

	public function getAfterEqualTo(adapter\call $call, $position)
	{
		$calls = new static();

		foreach ($this->getEqualTo($call)->toArray() as $innerPosition => $innerCall)
		{
			if ($innerPosition > $position)
			{
				$calls->setCall($innerCall, $innerPosition);
			}
		}

		return $calls;
	}

	public function getAfterIdenticalTo(adapter\call $call, $position)
	{
		$calls = new static();

		foreach ($this->getIdenticalTo($call)->toArray() as $innerPosition => $innerCall)
		{
			if ($innerPosition > $position)
			{
				$calls->setCall($innerCall, $innerPosition);
			}
		}

		return $calls;
	}

	public function getAfter(adapter\call $call, $position, $identical = false)
	{
		return ($identical === false ? $this->getAfterEqualTo($call, $position) : $this->getAfterIdenticalTo($call, $position));
	}

	public function hasAfterEqualTo(adapter\call $call, $position)
	{
		foreach ($this->getEqualTo($call)->toArray() as $innerPosition => $innerCall)
		{
			if ($innerPosition > $position)
			{
				return true;
			}
		}

		return false;
	}

	public function hasAfterIdenticalTo(adapter\call $call, $position)
	{
		foreach ($this->getIdenticalTo($call)->toArray() as $innerPosition => $innerCall)
		{
			if ($innerPosition > $position)
			{
				return true;
			}
		}

		return false;
	}

	public function hasAfter(adapter\call $call, $position, $identical = false)
	{
		return ($identical === false ? $this->hasAfterEqualTo($call, $position) : $this->hasAfterIdenticalTo($call, $position));
	}

	public function get(adapter\call $call, $identical = false)
	{
		return ($identical === false ? $this->getEqualTo($call) : $this->getIdenticalTo($call));
	}

	public function getTimeline()
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

	protected function setCall(adapter\call $call, $position = null)
	{
		$function = $call->getFunction();

		if ($function == '')
		{
			throw new exceptions\logic\invalidArgument('Function is undefined');
		}

		if (!isset(self::$callsNumber)) {
			return $this;
		}

		if ($position === null)
		{
			$position = ++self::$callsNumber;
		}

		$this->calls[self::getKey($call)][$position] = $call;
		$this->size++;

		return $this;
	}

	protected function getCalls($mixed)
	{
		$key = self::getKey($mixed);

		return (isset($this->calls[$key]) === false ? array() : $this->calls[$key]);
	}

	protected static function getKey(adapter\call $call)
	{
		return strtolower($call->getFunction());
	}

	private static function buildCallsForCall(adapter\call $call, array $array)
	{
		$calls = new static();

		$calls->calls[self::getKey($call)] = $array;
		$calls->size = sizeof($array);

		return $calls;
	}

	private static function buildCall($mixed)
	{
		return ($mixed instanceof adapter\call ? $mixed : new adapter\call($mixed));
	}
}
