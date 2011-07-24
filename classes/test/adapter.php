<?php

namespace mageekguy\atoum\test;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class adapter extends atoum\adapter
{
	protected $calls = array();
	protected $callers = array();

	public function __set($functionName, $mixed)
	{
		$this->{$functionName}->return = $mixed;
	}

	public function __get($functionName)
	{
		if (isset($this->{$functionName}) === false)
		{
			$this->callers[$functionName] = new adapter\caller();
		}

		return $this->callers[$functionName];
	}

	public function __isset($functionName)
	{
		return (isset($this->callers[$functionName]) === true);
	}

	public function __unset($functionName)
	{
		if (isset($this->{$functionName}) === true)
		{
			unset($this->callers[$functionName]);

			if (isset($this->calls[$functionName]) === true)
			{
				unset($this->calls[$functionName]);
			}
		}
	}

	public function getCallers()
	{
		return $this->callers;
	}

	public function getCalls($functionName = null)
	{
		return ($functionName === null ?  $this->calls : (isset($this->calls[$functionName]) === false ? null : $this->calls[$functionName]));
	}

	public function resetCalls()
	{
		$this->calls = array();

		return $this;
	}

	public function reset()
	{
		$this->callers = array();

		return $this->resetCalls();
	}

	public function addCall($functionName, array $arguments = array())
	{
		$this->calls[$functionName][] = $arguments;

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
