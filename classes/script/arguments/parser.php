<?php

namespace mageekguy\atoum\script\arguments;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;

class parser implements \iteratorAggregate
{
	protected $script = null;
	protected $values = array();
	protected $handlers = array();
	protected $arguments = array();

	public function __construct(atoum\superglobals $superglobals = null)
	{
		if ($superglobals === null)
		{
			$superglobals = new atoum\superglobals();
		}

		$this->setSuperglobals($superglobals);
	}

	public function setSuperglobals(atoum\superglobals $superglobals)
	{
		$this->superglobals = $superglobals;

		return $this;
	}

	public function setScript(atoum\script $script)
	{
		$this->script = $script;

		return $this;
	}

	public function getScript()
	{
		return $this->script;
	}

	public function getSuperglobals()
	{
		return $this->superglobals;
	}

	public function resetValues()
	{
		$this->values = array();

		return $this;
	}

	public function getHandlers()
	{
		return $this->handlers;
	}

	public function getIterator()
	{
		return new \arrayIterator($this->getValues());
	}

	public function parse(array $array = null)
	{
		if ($array === null)
		{
			$array = array_slice($this->superglobals->_SERVER['argv'], 1);
		}

		$this->resetValues();

		$arguments = new \arrayIterator($array);

		if (sizeof($arguments) > 0)
		{
			$value = $arguments->current();

			if (self::isArgument($value) === false)
			{
				throw new exceptions\runtime\unexpectedValue('First argument is invalid');
			}

			$argument = $value;

			$this->values[$argument] = array();

			$arguments->next();

			while ($arguments->valid() === true)
			{
				$value = $arguments->current();

				if (self::isArgument($value) === false)
				{
					$this->values[$argument][] = $value;
				}
				else
				{
					$this->triggerHandlers($argument);

					$argument = $value;

					$this->values[$argument] = array();
				}

				$arguments->next();
			}

			$this->triggerHandlers($argument);
		}

		return $this;
	}

	public function getValues($argument = null)
	{
		return ($argument === null ? $this->values : (isset($this->values[$argument]) === false ? null : $this->values[$argument]));
	}

	public function addHandler(\closure $handler, array $arguments)
	{
		$invoke = new \reflectionMethod($handler, '__invoke');

		if ($invoke->getNumberOfParameters() != 3)
		{
			throw new exceptions\runtime('Handler must take three arguments');
		}

		foreach ($arguments as $argument)
		{
			if (self::isArgument($argument) === false)
			{
				throw new exceptions\runtime('Argument \'' . $argument . '\' is invalid');
			}

			$this->handlers[$argument][] = $handler;
		}

		return $this;
	}

	public function argumentIsHandled($argument)
	{
		return (in_array($argument, $this->arguments) === true);
	}

	public function argumentsAreHandled(array $arguments)
	{
		return (sizeof(array_intersect($this->arguments, $arguments)) > 0);
	}

	public static function isArgument($value)
	{
		return (preg_match('/^(\+|-{1,2})[a-z][-_a-z0-9]*/i', $value) === 1);
	}

	protected function triggerHandlers($argument)
	{
		if (isset($this->handlers[$argument]) === true)
		{
			$this->arguments[] = $argument;

			foreach ($this->handlers[$argument] as $handler)
			{
				$handler->__invoke($this->script, $argument, $this->getValues($argument));
			}
		}

		return $this;
	}
}

?>
