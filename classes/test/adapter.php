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
		return $this->setInvoker($functionName, function() { return new invoker(); });
	}

	public function __isset($functionName)
	{
		return (isset($this->invokers[strtolower($functionName)]) === true);
	}

	public function __unset($functionName)
	{
		if (isset($this->{$functionName}) === true)
		{
			$functionName = strtolower($functionName);

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

	public function getCalls($functionName = null, array $arguments = null, $identical = false)
	{
		$calls = null;

		if ($functionName === null)
		{
			$calls = $this->calls;
		}
		else
		{
			$functionName = strtolower($functionName);

			if ($arguments !== null)
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

					if ($identical === false)
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

			if (isset($this->calls[$functionName]) === true)
			{
				if ($arguments === null)
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

	public function getTimeline($functionName = null)
	{
		$timeline = array();

		if ($functionName === null)
		{
			foreach ($this->calls as $calledFunctionName => $calls)
			{
				foreach ($calls as $number => $arguments)
				{
					$timeline[$number] = array($calledFunctionName => $arguments);
				}
			}
		}
		else
		{
			$functionName = strtolower($functionName);

			if (isset($this->calls[$functionName]) === true)
			{
				foreach ($this->calls[$functionName] as $number => $arguments)
				{
					$timeline[$number] = $arguments;
				}
			}
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
			$functionName = strtolower($functionName);

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
		$functionName = strtolower($functionName);

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

	protected function setInvoker($name, \closure $factory)
	{
		$name = strtolower($name);

		if (isset($this->invokers[$name]) === false)
		{
			$this->invokers[$name] = call_user_func($factory);
		}

		return $this->invokers[$name];
	}

	protected function callIsOverloaded($functionName, $call)
	{
		$functionName = strtolower($functionName);

		return (isset($this->invokers[$functionName]) === true && isset($this->invokers[$functionName][$call]) === true);
	}

	protected function nextCallIsOverloaded($functionName)
	{
		return ($this->callIsOverloaded($functionName, sizeof($this->getCalls($functionName)) + 1) === true);
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
}

adapter::setStorage();
