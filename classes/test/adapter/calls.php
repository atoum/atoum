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
		return $this->getEqualTo(static::buildCall($mixed));
	}

	public function offsetUnset($mixed)
	{
		$function = static::buildCall($mixed)->getFunction();

		if (isset($this->calls[$function]) === true)
		{
			unset($this->calls[$function]);
		}

		return $this;
	}

	public function offsetExists($mixed)
	{
		return (isset($this->calls[static::buildCall($mixed)->getFunction()]) === true);
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

		return $this->setCall($call, ++self::$callsNumber);
	}

	public function toArray(adapter\call $call = null, $identical = false)
	{
		$calls = array();

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
		$calls = new static();

		if (isset($this[$call]) === true)
		{
			foreach (array_filter($this->calls[$call->getFunction()], function($innerCall) use ($call) { return $call->isEqualTo($innerCall); }) as $position => $innerCall)
			{
				$calls->setCall($innerCall, $position);
			}
		}

		return $calls;
	}

	public function getIdenticalTo(adapter\call $call)
	{
		$calls = new static();

		if (isset($this[$call]) === true)
		{
			foreach (array_filter($this->calls[$call->getFunction()], function($innerCall) use ($call) { return $call->isIdenticalTo($innerCall); }) as $position => $innerCall)
			{
				$calls->setCall($innerCall, $position);
			}
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
			foreach ($this->calls[$call->getFunction()] as $position => $innerCall)
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
			foreach ($this->calls[$call->getFunction()] as $position => $innerCall)
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
			foreach (array_reverse($this->calls[$call->getFunction()], true) as $position => $innerCall)
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
			foreach (array_reverse($this->calls[$call->getFunction()], true) as $position => $innerCall)
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

	private function setCall(adapter\call $call, $position)
	{
		$function = $call->getFunction();

		if (isset($this->calls[$function][$position]) === false)
		{
			$this->size++;
		}

		$this->calls[$call->getFunction()][$position] = $call;

		return $this;
	}

	private static function buildCall($mixed)
	{
		return ($mixed instanceof adapter\call ? $mixed : new adapter\call($mixed));
	}
}
