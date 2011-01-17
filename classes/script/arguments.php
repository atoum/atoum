<?php

namespace mageekguy\atoum\script;

use \mageekguy\atoum\exceptions;

class arguments implements \iteratorAggregate
{
	protected $values = array();
	protected $handlers = array();

	public function __construct() {}

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

	public function parse(array $array)
	{
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

	public function addHandler($argument, \closure $handler)
	{
		if (self::isArgument($argument) === false)
		{
			throw new exceptions\runtime('Argument \'' . $argument . '\' is invalid');
		}

		$invoke = new \reflectionMethod($handler, '__invoke');

		if ($invoke->getNumberOfParameters() != 2)
		{
			throw new exceptions\runtime('Handler of argument \'' . $argument . '\' must take two argument');
		}

		$this->handlers[$argument][] = $handler;

		return $this;
	}

	public static function isArgument($value)
	{
		return (preg_match('/^(\+|-{1,2})[a-z][-_a-z0-9]*/i', $value) === 1);
	}

	protected function triggerHandlers($argument)
	{
		if (isset($this->handlers[$argument]) === true)
		{
			foreach ($this->handlers[$argument] as $handler)
			{
				$handler->__invoke($argument, $this->getValues($argument));
			}
		}

		return $this;
	}
}

?>
