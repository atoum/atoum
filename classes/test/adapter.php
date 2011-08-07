<?php

namespace mageekguy\atoum\test;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class adapter extends atoum\adapter
{
	protected $calls = array();
	protected $callables = array();

	private static $callsNumber = 0;

	public function __set($functionName, $mixed)
	{
		$this->{$functionName}->return = $mixed;
	}

	public function __get($functionName)
	{
		if (isset($this->{$functionName}) === false)
		{
			$this->callables[$functionName] = new adapter\callable();
		}

		return $this->callables[$functionName];
	}

	public function __isset($functionName)
	{
		return (isset($this->callables[$functionName]) === true);
	}

	public function __unset($functionName)
	{
		if (isset($this->{$functionName}) === true)
		{
			unset($this->callables[$functionName]);

			if (isset($this->calls[$functionName]) === true)
			{
				unset($this->calls[$functionName]);
			}
		}
	}

	public function getCallables()
	{
		return $this->callables;
	}

	public function getCalls($functionName = null, array $arguments = null)
	{
		$calls = null;

		if ($functionName === null)
		{
			$calls = $this->calls;
		}
		else if (isset($this->calls[$functionName]) === true)
		{
			if ($arguments === null)
			{
				$calls = $this->calls[$functionName];
			}
			else
			{
				$calls = array_filter($this->calls[$functionName], function($callArguments) use ($arguments) { return $arguments == $callArguments; });
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
		$this->callables = array();

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
			throw new exceptions\logic\invalidArgument('Function \'' . $functionName . '()\' is not callable by an adapter');
		}

		$this->addCall($functionName, $arguments);

		return (isset($this->{$functionName}) === false ? parent::invoke($functionName, $arguments) : $this->{$functionName}->invoke($arguments, sizeof($this->calls[$functionName])));
	}

	public static function getCallsNumber()
	{
		return self::$callsNumber;
	}

	protected static function isLanguageConstruct($functionName)
	{
		switch ($functionName)
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
