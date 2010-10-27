<?php

namespace mageekguy\atoum\asserters;

class castToString extends \mageekguy\atoum\asserters\string
{
	public function setWith($variable, $charlist = null, $checkType = true)
	{
		parent::setWith($variable, $charlist, false);

		if ($checkType === true)
		{
			if (self::isObject($variable) === false)
			{
				$this->fail(sprintf($this->locale->_('%s is not an object'), $this->toString($variable)));
			}
			else
			{
				$this->pass();
				$this->variable = (string) $variable;
			}
		}

		return $this;
	}

	protected static function isObject($variable)
	{
		return (is_object($variable) === true);
	}
}

?>
