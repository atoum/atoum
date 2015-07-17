<?php

namespace mageekguy\atoum\test;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\adapter\invoker
;

class adapter extends atoum\adapter
{
	protected $calls = null;
	protected $invokers = array();

	private static $storage = null;

	public function __construct()
	{
		$this->setCalls();

		if (self::$storage !== null)
		{
			self::$storage->add($this);
		}
	}

	public function __clone()
	{
		$this->calls = clone $this->calls;

		if (self::$storage !== null)
		{
			self::$storage->add($this);
		}
	}

	public function __set($functionName, $mixed)
	{
		$this->{$functionName}->return = $mixed;

		return $this;
	}

	public function __get($functionName)
	{
		return $this->setInvoker($functionName);
	}

	public function __isset($functionName)
	{
		return $this->nextCallIsOverloaded($functionName);
	}

	public function __unset($functionName)
	{
		if (isset($this->{$functionName}) === true)
		{
			$functionName = static::getKey($functionName);

			unset($this->invokers[$functionName]);
			unset($this->calls[$functionName]);
		}

		return $this;
	}

	public function __sleep()
	{
		return array();
	}

	public function __toString()
	{
		return (string) $this->calls;
	}

	public function getInvokers()
	{
		return $this->invokers;
	}

	public function setCalls(adapter\calls $calls = null)
	{
		$this->calls = $calls ?: new adapter\calls();

		return $this->resetCalls();
	}

	public function getCalls(adapter\call $call = null, $identical = false)
	{
		return ($call === null ? $this->calls : $this->calls->get($call, $identical));
	}

	public function getCallsNumber(adapter\call $call = null, $identical = false)
	{
		return sizeof($this->getCalls($call, $identical));
	}

	public function getCallsEqualTo(adapter\call $call)
	{
		return $this->calls->getEqualTo($call);
	}

	public function getCallsNumberEqualTo(adapter\call $call)
	{
		return sizeof($this->calls->getEqualTo($call));
	}

	public function getCallsIdenticalTo(adapter\call $call)
	{
		return $this->calls->getIdenticalTo($call);
	}

	public function getPreviousCalls(adapter\call $call, $position, $identical = false)
	{
		return $this->calls->getPrevious($call, $position, $identical);
	}

	public function hasPreviousCalls(adapter\call $call, $position, $identical = false)
	{
		return $this->calls->hasPrevious($call, $position, $identical);
	}

	public function getAfterCalls(adapter\call $call, $position, $identical = false)
	{
		return $this->calls->getAfter($call, $position, $identical);
	}

	public function hasAfterCalls(adapter\call $call, $position, $identical = false)
	{
		return $this->calls->hasAfter($call, $position, $identical);
	}

	public function getCallNumber(adapter\call $call = null, $identical = false)
	{
		return sizeof($this->getCalls($call, $identical));
	}

	public function getTimeline(adapter\call $call = null, $identical = false)
	{
		return $this->calls->getTimeline($call, $identical);
	}

	public function resetCalls($functionName = null)
	{
		if ($functionName === null)
		{
			$this->calls->reset();
		}
		else
		{
			unset($this->calls[$functionName]);
		}

		return $this;
	}

	public function reset()
	{
		$this->invokers = array();

		return $this->resetCalls();
	}

	public function addCall($functionName, array $arguments = array())
	{
		$unreferencedArguments = array();

		foreach ($arguments as $argument)
		{
			$unreferencedArguments[] = $argument;
		}

		$this->calls[] = $this->buildCall($functionName, $unreferencedArguments);

		return $this;
	}

	public function invoke($functionName, array $arguments = array())
	{
		if (self::isLanguageConstruct($functionName) || (function_exists($functionName) === true && is_callable($functionName) === false))
		{
			throw new exceptions\logic\invalidArgument('Function \'' . $functionName . '()\' is not invokable by an adapter');
		}

		$call = sizeof($this->addCall($functionName, $arguments)->getCallsEqualTo(new adapter\call($functionName)));

		try
		{
			return ($this->callIsOverloaded($functionName, $call) === false ? parent::invoke($functionName, $arguments) : $this->{$functionName}->invoke($arguments, $call));
		}
		catch (exceptions\logic\invalidArgument $exception)
		{
			throw new exceptions\logic('There is no return value defined for \'' . $functionName . '() at call ' . $call);
		}
	}

	public static function setStorage(adapter\storage $storage = null)
	{
		self::$storage = $storage ?: new adapter\storage();
	}

	protected function buildInvoker($functionName, \closure $factory = null)
	{
		if ($factory === null)
		{
			$factory = function($functionName) { return new invoker($functionName); };
		}

		return $factory($functionName);
	}

	protected function setInvoker($functionName, \closure $factory = null)
	{
		$key = static::getKey($functionName);

		if (isset($this->invokers[$key]) === false)
		{
			$this->invokers[$key] = $this->buildInvoker($functionName, $factory);
		}

		return $this->invokers[$key];
	}

	protected function callIsOverloaded($functionName, $call)
	{
		$functionName = static::getKey($functionName);

		return (isset($this->invokers[$functionName]) === true && $this->invokers[$functionName]->closureIsSetForCall($call) === true);
	}

	protected function nextCallIsOverloaded($functionName)
	{
		return ($this->callIsOverloaded($functionName, $this->getCallNumber(new adapter\call($functionName)) + 1) === true);
	}

	protected function buildCall($function, array $arguments)
	{
		return new adapter\call($function, $arguments);
	}

	protected static function getKey($functionName)
	{
		return strtolower($functionName);
	}

	protected static function isLanguageConstruct($functionName)
	{
		switch (strtolower($functionName))
		{
			case 'array':
			case 'declare':
			case 'echo':
			case 'empty':
			case 'eval':
			case 'exit':
			case 'die':
			case 'isset':
			case 'list':
			case 'print':
			case 'unset':
			case 'require':
			case 'require_once':
			case 'include':
			case 'include_once':
				return true;

			default:
				return false;
		}
	}

	protected static function getArgumentsFilter($arguments, $identicalArguments)
	{
		$filter = null;

		if (is_array($arguments) === true)
		{
			if ($arguments === array())
			{
				$filter = function($callArguments) use ($arguments) {
					return ($arguments === $callArguments);
				};
			}
			else
			{
				$callback = function($a, $b) {
					return ($a == $b ? 0 : -1);
				};

				if ($identicalArguments === false)
				{
					$filter = function($callArguments) use ($arguments, $callback) {
						return ($arguments == array_uintersect_uassoc($callArguments, $arguments, $callback, $callback));
					};
				}
				else
				{
					$filter = function($callArguments) use ($arguments, $callback) {
						return ($arguments === array_uintersect_uassoc($callArguments, $arguments, $callback, $callback));
					};
				}
			}
		}

		return $filter;
	}
}
