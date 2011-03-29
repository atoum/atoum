<?php

namespace mageekguy\atoum\test;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class adapter extends atoum\adapter
{
	protected $calls = array();
	protected $functions = array();

	public function __set($functionName, $mixed)
	{
		if ($mixed instanceof \closure === false)
		{
			$mixed = function() use ($mixed) { return $mixed; };
		}

		$this->{$functionName}->setClosure($mixed);
	}

	public function __get($functionName)
	{
		if (isset($this->{$functionName}) === false)
		{
			$this->functions[$functionName] = new atoum\adapter\caller();
		}

		return $this->functions[$functionName];
	}

	public function __isset($functionName)
	{
		return (isset($this->functions[$functionName]) === true);
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

	public function invoke($functionName, array $arguments = array())
	{
		if (self::isLanguageConstruct($functionName) || (function_exists($functionName) === true && is_callable($functionName) === false))
		{
			throw new exceptions\logic\invalidArgument('Function \'' . $functionName . '()\' is not callable by an adapter');
		}

		$this->calls[$functionName][] = $arguments;

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
