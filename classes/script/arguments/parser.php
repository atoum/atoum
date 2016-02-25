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
	protected $defaultHandler = null;
	protected $priorities = array();
	protected $superglobals = null;

	public function __construct(atoum\superglobals $superglobals = null)
	{
		$this->setSuperglobals($superglobals ?: new atoum\superglobals());
	}

	public function __toString()
	{
		$string = '';

		foreach ($this->values as $argumentName => $argumentValues)
		{
			$string .= ($string == '' ? '' : ' ') . $argumentName;

			foreach ($argumentValues as $argumentValue)
			{
				$string .= ' ' . $argumentValue;
			}
		}

		return $string;
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

	public function getPriorities()
	{
		return $this->priorities;
	}

	public function getIterator()
	{
		return new \arrayIterator($this->getValues());
	}

	public function parse(atoum\script $script, array $array = array())
	{
		$this->init($array);

		$priorities = $this->priorities;

		$values = $this->values;

		uksort($values, function($arg1, $arg2) use ($priorities) {
				switch (true)
				{
					case isset($priorities[$arg1]) === false && isset($priorities[$arg2]) === false:
						return 0;
					case isset($priorities[$arg1]) === false && isset($priorities[$arg2]) === true:
						return 1;
					case isset($priorities[$arg2]) === false && isset($priorities[$arg1]) === true:
						return -1;

					default:
						return ($priorities[$arg1] > $priorities[$arg2] ? -1 : ($priorities[$arg1] == $priorities[$arg2] ? 0 : 1));
				}
			}
		);

		foreach ($values as $argument => $value)
		{
			$this->triggerHandlers($argument, $value, $script);
		}

		return $this;
	}

	public function getValues($argument = null)
	{
		return ($argument === null ? $this->values : (isset($this->values[$argument]) === false ? null : $this->values[$argument]));
	}

	public function hasFoundArguments()
	{
		return (sizeof($this->values) > 0);
	}

	public function addHandler(\closure $handler, array $arguments, $priority = 0)
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
			$this->priorities[$argument] = (int) $priority;
		}

		return $this;
	}

	public function setDefaultHandler(\closure $handler)
	{
		$reflectedHandler = new \reflectionFunction($handler);

		if ($reflectedHandler->getNumberOfParameters() < 2)
		{
			throw new exceptions\runtime('Handler must take two arguments');
		}

		$this->defaultHandler = $handler;

		return $this;
	}

	public function getDefaultHandler()
	{
		return $this->defaultHandler;
	}

	public function resetHandlers()
	{
		$this->handlers = array();
		$this->defaultHandler = null;
		$this->priorities = array();

		return $this;
	}

	public function argumentIsHandled($argument)
	{
		return (isset($this->handlers[$argument]) === true || $this->defaultHandler !== null);
	}

	public function argumentHasHandler($argument)
	{
		return (isset($this->handlers[$argument]) === true);
	}

	public function init(array $array = array())
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

			if (self::isArgument($value) === false && $this->defaultHandler === null)
			{
				throw new exceptions\runtime\unexpectedValue('Argument \'' . $value . '\' is invalid');
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
					$argument = $value;

					if (isset($this->values[$argument]) === false) {
						$this->values[$argument] = array();
					}
				}

				$arguments->next();
			}
		}

		return $this;
	}

	public function triggerHandlers($argument, array $values, atoum\script $script, & $argumentUsed = null)
	{
		if (isset($this->handlers[$argument]) === true)
		{
			$this->invokeHandlers($script, $argument, $values);
		}
		else
		{
			$closestArgument = $this->getClosestArgument($argument, $min);

			switch (true)
			{
				case $closestArgument === null:
					if ($this->defaultHandler === null || $this->defaultHandler->__invoke($script, $argument) === false)
					{
						throw new exceptions\runtime\unexpectedValue('Argument \'' . $argument . '\' is unknown');
					}
					break;

				case $min > 0:
					throw new exceptions\runtime\unexpectedValue('Argument \'' . $argument . '\' is unknown, did you mean \'' . $closestArgument . '\'?');

				default:
					$this->invokeHandlers($script, $closestArgument, $values);
			}
		}

		return $this;
	}

	public function getClosestArgument($argument, & $min = null)
	{
		$closestArgument = null;

		if (self::isArgument($argument) === true)
		{
			$argumentMetaphone = metaphone($argument);
			$availableArguments = array_keys($this->handlers);

			natsort($availableArguments);

			foreach ($availableArguments as $handlerArgument)
			{
				$levenshtein = levenshtein($argumentMetaphone, metaphone($handlerArgument));

				if ($levenshtein < (strlen($argument) / 2))
				{
					if ($min === null || $levenshtein < $min)
					{
						$min = $levenshtein;
						$closestArgument = $handlerArgument;
					}
				}
			}
		}

		return $closestArgument;
	}

	public function invokeHandlers(atoum\script $script, $argument, array $values)
	{
		$position = array_search($argument, array_keys($this->values)) + 1;

		foreach ($this->handlers[$argument] as $handler)
		{
			$handler->__invoke($script, $argument, $values, $position);
		}

		return $this;
	}

	public static function isArgument($value)
	{
		return (preg_match('/^(\+{1,}|-{1,})[a-z][-_a-z0-9]*/i', $value) === 1);
	}
}
