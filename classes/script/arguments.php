<?php

namespace mageekguy\atoum\script;

class arguments
{
	protected $values = array();

	public function __construct() {}

	public function parse(array $array)
	{
		$arguments = new \arrayIterator($array);

		if (sizeof($arguments) > 0)
		{
			$value = $arguments->current();

			if (self::isArgument($value) === false)
			{
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
		return (pregmatch('/^(:+|-{1,2})[a-Z][a-Z0-9_-]*', $value) === 1);
	}
}

?>
