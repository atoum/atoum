<?php

namespace mageekguy\atoum\script\arguments;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class parser implements \iteratorAggregate
{
	protected $script = null;
	protected $values = array();
	protected $handlers = array();

	public function __construct(atoum\superglobals $superglobals = null)
	{
		$this->setSuperglobals($superglobals ?: new atoum\superglobals());
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

			$key = 0;

			while ($arguments->valid() === true)
			{
				$value = $arguments->current();

				if (self::isArgument($value) === false)
				{
					$this->values[$argument][] = $value;
				}
				else
				{
					$this->triggerHandlers();

					$argument = $value;

					$this->values[$argument] = array();
				}

				$arguments->next();
			}

			$this->triggerHandlers();
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

		if ($invoke->getNumberOfParameters() < 3)
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
		return (isset($this->values[$argument]) === true);
	}

	public function argumentsAreHandled(array $arguments)
	{
		return (sizeof(array_intersect(array_keys($this->values), $arguments)) > 0);
	}

	public static function isArgument($value)
	{
		return (preg_match('/^(\+|-{1,2})[a-z][-_a-z0-9]*/i', $value) === 1);
	}

	protected function triggerHandlers()
	{
		$lastArgument = array_slice($this->values, -1);

		list($argument, $values) = each($lastArgument);

		if (isset($this->handlers[$argument]) === false)
		{
			unset($this->values[$argument]);
		}
		else
		{
			foreach ($this->handlers[$argument] as $handler)
			{
				$handler->__invoke($this->script, $argument, $values, sizeof($this->values));
			}
		}

		return $this;
	}
}

?>
