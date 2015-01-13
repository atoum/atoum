<?php

namespace mageekguy\atoum\test\adapter;

use
	mageekguy\atoum\exceptions
;

class invoker implements \arrayAccess, \countable
{
	protected $function = '';
	protected $bindClosureTo = null;
	protected $currentCall = null;
	protected $closuresByCall = array();

	public function __construct($function)
	{
		$this->function = (string) $function;
	}

	public function __get($keyword)
	{
		return $this->{$keyword}();
	}

	public function __set($keyword, $mixed)
	{
		switch ($keyword)
		{
			case 'return':
				if ($mixed instanceof \closure === false)
				{
					$mixed = function() use ($mixed) { return $mixed; };
				}
				break;

			case 'throw':
				if ($mixed instanceof \closure === false)
				{
					$mixed = function() use ($mixed) { throw $mixed; };
				}
				break;

			default:
				throw new exceptions\logic\invalidArgument('Keyword \'' . $keyword . '\' is unknown');
		}

		return $this->setClosure($mixed);
	}

	public function getFunction()
	{
		return $this->function;
	}

	public function bindTo($object)
	{
		$this->bindClosureTo = $object;

		foreach ($this->closuresByCall as & $closure)
		{
			$closure = $this->bindClosure($closure);
		}

		return $this;
	}

	public function count()
	{
		return sizeof($this->closuresByCall);
	}

	public function doesNothing()
	{
		return $this->setClosure(function() {});
	}

	public function doesSomething()
	{
		return $this->unsetClosure();
	}

	public function isEmpty()
	{
		return (sizeof($this->closuresByCall) <= 0);
	}

	public function getCurrentCall()
	{
		return $this->currentCall;
	}

	public function setClosure(\closure $closure, $call = 0)
	{
		if ($this->currentCall !== null)
		{
			$call = $this->currentCall;
			$this->currentCall = null;
		}

		static::checkCall($call);

		$closure = $this->bindClosure($closure);

		if ($call === null && sizeof($this->closuresByCall) <= 0)
		{
			$call = 1;
		}

		if ($call === null)
		{
			$this->closuresByCall[] = $closure;
		}
		else
		{
			$this->closuresByCall[$call] = $closure;
		}

		return $this;
	}

	public function getClosure($call = 0)
	{
		$call = static::checkCall($call);

		return (isset($this->closuresByCall[$call]) === true ? $this->closuresByCall[$call] : (isset($this->closuresByCall[0]) === false ? null : $this->closuresByCall[0]));
	}

	public function closureIsSetForCall($call = 0)
	{
		static::checkCall($call);

		$closureIsSet = (isset($this->closuresByCall[$call]) === true);

		if ($closureIsSet === false && $call > 0)
		{
			$closureIsSet = (isset($this->closuresByCall[0]) === true);
		}

		return $closureIsSet;
	}

	public function unsetClosure($call = 0)
	{
		if ($this->closureIsSetForCall($call) === false)
		{
			throw new exceptions\logic\invalidArgument('There is no closure defined for call ' . $call);
		}

		unset($this->closuresByCall[$call]);

		return $this;
	}

	public function offsetSet($call = null, $mixed = null)
	{
		if ($mixed instanceof \closure === false)
		{
			$mixed = function() use ($mixed) { return $mixed; };
		}

		return $this->setClosure($mixed, $call);
	}

	public function offsetGet($call)
	{
		return $this->atCall($call);
	}

	public function offsetUnset($call)
	{
		return $this->unsetClosure($call);
	}

	public function offsetExists($call)
	{
		return $this->closureIsSetForCall($call) ?: $this->closureIsSetForCall(0);
	}

	public function atCall($call)
	{
		$this->currentCall = self::checkCall($call);

		return $this;
	}

	public function invoke(array $arguments = array(), $call = 0)
	{
		if ($this->closureIsSetForCall($call) === false)
		{
			throw new exceptions\logic\invalidArgument('There is no closure defined for call ' . $call);
		}

		return call_user_func_array($this->getClosure($call), $arguments);
	}

	protected function bindClosure(\closure $closure)
	{
		if ($this->bindClosureTo !== null && static::isBindable($closure) === true)
		{
			$closure = $closure->bindTo($this->bindClosureTo);
		}

		return $closure;
	}

	protected static function checkCall($call)
	{
		$call = (int) $call;

		if ($call < 0)
		{
			throw new exceptions\logic\invalidArgument('Call number must be greater than or equal to zero');
		}

		return $call;
	}

	protected static function isBindable(\closure $closure)
	{
		$isBindable = (version_compare(PHP_VERSION, '5.4.0') >= 0);

		if ($isBindable === true)
		{
			$reflectedClosure = new \reflectionFunction($closure);

			$isBindable = ($reflectedClosure->getClosureThis() !== null || $reflectedClosure->getClosureScopeClass() === null);
		}

		return $isBindable;
	}
}
