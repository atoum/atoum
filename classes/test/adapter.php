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

	private static $callsNumber = 0;
	private static $instances = null;

	public function __construct()
	{
		if (self::$instances === null)
		{
			self::$instances = new \splObjectStorage();
		}

		self::$instances->attach($this);
	}

	public function __destruct()
	{
		self::$instances->detach($this);
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
		return (isset($this->invokers[strtolower($functionName)]) === false ? false : isset($this->invokers[strtolower($functionName)][0]));
	}

	public function __unset($functionName)
	{
		if (isset($this->{$functionName}) === true)
		{
			$functionName = strtolower($functionName);

			unset($this->invokers[$functionName]);

			foreach ($this->calls as $callName => $closure)
			{
				if ($functionName == strtolower($callName))
				{
					unset($this->calls[$callName]);
				}
			}
		}

		return $this;
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

			foreach ($this->calls as $callName => $callArguments)
			{
				if ($functionName == strtolower($callName))
				{
					if ($arguments === null)
					{
						$calls = $callArguments;
					}
					else
					{
						if ($identical === false)
						{
							$filter = function($callArguments) use ($arguments) {
								return ($arguments == array_slice($callArguments, 0, sizeof($arguments)));
							};
						}
						else
						{
							$filter = function($callArguments) use ($arguments) {
								return ($arguments === array_slice($callArguments, 0, sizeof($arguments)));
							};
						}

						$calls = array_filter($callArguments, $filter);
					}

					break;
				}
			}
		}

		return $calls;
	}

	public function getTimeline($functionName = null)
	{
		$timeline = array();

		foreach ($this->calls as $calledFunctionName => $calls)
		{
			if ($functionName === null)
			{
				foreach ($calls as $number => $arguments)
				{
					$timeline[$number] = array($calledFunctionName => $arguments);
				}
			}
			else if ($calledFunctionName === $functionName)
			{
				foreach ($calls as $number => $arguments)
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
		else if (isset($this->calls[$functionName]) === true)
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
		$this->calls[$functionName][++self::$callsNumber] = $arguments;

		return $this;
	}

	public function invoke($functionName, array $arguments = array())
	{
		if (self::isLanguageConstruct($functionName) || (function_exists($functionName) === true && is_callable($functionName) === false))
		{
			throw new exceptions\logic\invalidArgument('Function \'' . $functionName . '()\' is not invokable by an adapter');
		}

		$this->addCall($functionName, $arguments);

		$call = sizeof($this->getCalls($functionName));

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

	public static function resetCallsForAllInstances()
	{
		if (self::$instances !== null)
		{
			foreach (self::$instances as $instance)
			{
				$instance->resetCalls();
			}
		}
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
			case 'echo':
			case 'empty':
			case 'eval':
			case 'exit':
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
