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
		self::$storage->add($this->setCalls());
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
			$functionName = static::normalizeFunctionName($functionName);

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

	public function getCallsEqualTo(adapter\call $call)
	{
		return $this->calls->getEqualTo($call);
	}

	public function getCallsIdenticalTo(adapter\call $call)
	{
		return $this->calls->getIdenticalTo($call);
	}

	public function getFirstCallEqualTo(adapter\call $call)
	{
		return $this->calls->getFirstEqualTo($call);
	}

	public function getFirstCallIdenticalTo(adapter\call $call)
	{
		return $this->calls->getFirstIdenticalTo($call);
	}

	public function getLastCallEqualTo(adapter\call $call)
	{
		return $this->calls->getLastEqualTo($call);
	}

	public function getLastCallIdenticalTo(adapter\call $call)
	{
		return $this->calls->getLastIdenticalTo($call);
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

		$this->calls[] = $call = new adapter\call($functionName, $unreferencedArguments);

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

	protected function setInvoker($name, \closure $factory = null)
	{
		$name = static::normalizeFunctionName($name);

		if (isset($this->invokers[$name]) === false)
		{
			if ($factory === null)
			{
				$factory = function() { return new invoker(); };
			}

			$this->invokers[$name] = $factory();
		}

		return $this->invokers[$name];
	}

	protected function callIsOverloaded($functionName, $call)
	{
		$functionName = static::normalizeFunctionName($functionName);

		return (isset($this->invokers[$functionName]) === true && $this->invokers[$functionName]->closureIsSetForCall($call) === true);
	}

	protected function nextCallIsOverloaded($functionName)
	{
		return ($this->callIsOverloaded($functionName, $this->getCallNumber(new adapter\call($functionName)) + 1) === true);
	}

	protected static function normalizeFunctionName($functionName)
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

adapter::setStorage();
