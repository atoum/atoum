<?php

namespace mageekguy\atoum\test;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;

class adapter extends atoum\adapter
{
	protected $calls = array();
	protected $functions = array();

	public function __set($functionName, $closure)
	{
		$this->functions[$functionName] = $closure;
	}

	public function __get($functionName)
	{
		return (isset($this->{$functionName}) === false ? null : $this->functions[$functionName]);
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

		return (array_key_exists($functionName, $this->functions) === false ? call_user_func_array($functionName, $arguments) : ($this->functions[$functionName] instanceof \closure === false ? $this->functions[$functionName] : call_user_func_array($this->functions[$functionName], $arguments)));
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
