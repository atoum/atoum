<?php

namespace mageekguy\atoum\test;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\adapter\invoker
;

class adapter extends atoum\adapter
{
	protected $calls = array();
	protected $invokers = array();

	private static $storage = null;
	private static $callsNumber = 0;

	public function __construct()
	{
		self::$storage->add($this);
	}

	public function __set($functionName, $mixed)
	{
		$this->__get($functionName)->return = $mixed;

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

			if (isset($this->calls[$functionName]) === true)
			{
				unset($this->calls[$functionName]);
			}
		}

		return $this;
	}

	public function __sleep()
	{
		return array();
	}

	public function getInvokers()
	{
		return $this->invokers;
	}

	public function getCalls($functionName = null, array $arguments = null, $identicalArguments = false)
	{
		$calls = null;

		if ($functionName === null)
		{
			$calls = $this->calls;
		}
		else
		{
			$functionName = static::normalizeFunctionName($functionName);

			if (isset($this->calls[$functionName]) === true)
			{
				$filter = static::getArgumentsFilter($arguments, $identicalArguments);

				if ($filter === null)
				{
					$calls = $this->calls[$functionName];
				}
				else
				{
					$calls = array_filter($this->calls[$functionName], $filter);
				}
			}
		}

		return $calls;
	}

	public function getCallNumber($functionName = null, array $arguments = null, $identicalArguments = false)
	{
		return sizeof($this->getCalls($functionName, $arguments, $identicalArguments));
	}

	public function getTimeline($functionName = null, array $arguments = null, $identicalArguments = false)
	{
		$timeline = array();

		if ($functionName === null)
		{
			foreach ($this->calls as $calledFunctionName => $calls)
			{
				foreach ($calls as $number => $callArguments)
				{
					$timeline[$number] = array($calledFunctionName => $callArguments);
				}
			}
		}
		else
		{
			$functionName = static::normalizeFunctionName($functionName);

			if (isset($this->calls[$functionName]) === true)
			{
				foreach ($this->calls[$functionName] as $number => $callArguments)
				{
					$timeline[$number] = $callArguments;
				}
			}
		}

		$filter = static::getArgumentsFilter($arguments, $identicalArguments);

		if ($filter !== null)
		{
			$timeline = array_filter($timeline, $filter);
		}

		ksort($timeline, SORT_NUMERIC);

		return $timeline;
	}

	public function resetCalls($functionName = null)
	{
		if ($functionName === null)
		{
			$this->calls = array();
		}
		else
		{
			$functionName = static::normalizeFunctionName($functionName);

			if (isset($this->calls[$functionName]) === true)
			{
				unset($this->calls[$functionName]);
			}
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
		$functionName = static::normalizeFunctionName($functionName);

		$unreferencedArguments = array();

		foreach ($arguments as $argument)
		{
			$unreferencedArguments[] = $argument;
		}

		$this->calls[$functionName][++self::$callsNumber] = $unreferencedArguments;

		return sizeof($this->calls[$functionName]);
	}

	public function invoke($functionName, array $arguments = array())
	{
		if (self::isLanguageConstruct($functionName) || (function_exists($functionName) === true && is_callable($functionName) === false))
		{
			throw new exceptions\logic\invalidArgument('Function \'' . $functionName . '()\' is not invokable by an adapter');
		}

		$call = $this->addCall($functionName, $arguments);

		try
		{
			return ($this->callIsOverloaded($functionName, $call) === false ? parent::invoke($functionName, $arguments) : $this->{$functionName}->invoke($arguments, $call));
		}
		catch (exceptions\logic\invalidArgument $exception)
		{
			throw new exceptions\logic('There is no return value defined for \'' . $functionName . '() at call ' . $call);
		}
	}

	public static function getCallsNumber()
	{
		return self::$callsNumber;
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
		return ($this->callIsOverloaded($functionName, $this->getCallNumber($functionName) + 1) === true);
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
