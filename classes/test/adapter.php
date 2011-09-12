<?php

namespace mageekguy\atoum\test;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class adapter extends atoum\adapter
{
	protected $calls = array();
	protected $invokers = array();

	private static $callsNumber = 0;
	private static $instances = array();

	public function __construct()
	{
		self::$instances[] = $this;
	}

	public function __set($functionName, $mixed)
	{
		$this->__get($functionName)->return = $mixed;

		return $this;
	}

	public function __get($functionName)
	{
		$functionName = strtolower($functionName);

		if (isset($this->invokers[$functionName]) === false)
		{
			$this->invokers[$functionName] = new adapter\invoker();
		}

		return $this->invokers[$functionName];
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

	public function getCalls($functionName = null, array $arguments = null)
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
						$calls = array_filter($callArguments, function($callArguments) use ($arguments) { return $arguments == $callArguments; });
					}

					break;
				}
			}
		}

		return $calls;
	}

	public function resetCalls()
	{
		$this->calls = array();

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

		return (isset($this->{$functionName}) === false ? parent::invoke($functionName, $arguments) : $this->{$functionName}->invoke($arguments, sizeof($this->calls[$functionName])));
	}

	public static function getCallsNumber()
	{
		return self::$callsNumber;
	}

	public static function resetCallsForAllInstances()
	{
		foreach (self::$instances as $instance)
		{
			$instance->resetCalls();
		}
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

?>
