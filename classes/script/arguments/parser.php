<?php

namespace mageekguy\atoum\script\arguments;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class parser implements \iteratorAggregate
{
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

	public function parse(atoum\script $script, array $array = array())
	{
		if (sizeof($array) <= 0)
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
				throw new exceptions\runtime\unexpectedValue('First argument \'' . $value . '\' is invalid');
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
					$this->triggerHandlers($script);

					$argument = $value;

					$this->values[$argument] = array();
				}

				$arguments->next();
			}

			$this->triggerHandlers($script);
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

	public function resetHandlers()
	{
		$this->handlers = array();

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

	protected function triggerHandlers(atoum\script $script)
	{
		$lastArgument = array_slice($this->values, -1);

		list($argument, $values) = each($lastArgument);

		if (isset($this->handlers[$argument]) === true)
		{
			$this->invokeHandlers($script, $argument, $values);
		}
		else
		{
			$argumentMetaphone = metaphone($argument);

			$min = null;
			$closestArgument = null;
			$handlerArguments = array_keys($this->handlers);

			natsort($handlerArguments);

			foreach ($handlerArguments as $handlerArgument)
			{
				$levenshtein = levenshtein($argumentMetaphone, metaphone($handlerArgument));

				if ($min === null || $levenshtein < $min)
				{
					$min = $levenshtein;
					$closestArgument = $handlerArgument;
				}
			}

			if ($closestArgument === null)
			{
				throw new exceptions\runtime\unexpectedValue('Argument \'' . $argument . '\' is unknown');
			}
			else if ($min > 0)
			{
				throw new exceptions\runtime\unexpectedValue('Argument \'' . $argument . '\' is unknown, did you mean \'' . $closestArgument . '\' ?');
			}
			else
			{
				$this->invokeHandlers($script, $closestArgument, $values);
			}
		}

		return $this;
	}

	protected function invokeHandlers(atoum\script $script, $argument, array $values)
	{
		foreach ($this->handlers[$argument] as $handler)
		{
			$handler->__invoke($script, $argument, $values, sizeof($this->values));
		}

		return $this;
	}
}

?>
