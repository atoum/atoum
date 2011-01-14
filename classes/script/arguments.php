<?php

namespace mageekguy\atoum\script;

use \mageekguy\atoum\exceptions;

class arguments
{
	protected $values = array();

	public function __construct() {}

	public function resetValues()
	{
		$this->values = array();

		return $this;
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
					$argument = $value;

					$this->values[$argument] = array();
				}

				$arguments->next();
			}
		}

		return $this;
	}

	public function getValues()
	{
		return $this->values;
	}

	public static function isArgument($value)
	{
		return (preg_match('/^(\+|-{1,2})[a-z][-_a-z0-9]*/i', $value) === 1);
	}
}

?>
