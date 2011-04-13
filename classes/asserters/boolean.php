<?php

namespace mageekguy\atoum\asserters;

use \mageekguy\atoum\exceptions;

class boolean extends \mageekguy\atoum\asserters\variable
{
	public function setWith($variable, $label = null)
	{
		parent::setWith($variable, $label);

		if (self::isBoolean($this->variable) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a boolean'), $this));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function isTrue($failMessage = null)
	{
		return $this->isEqualTo(true, $failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not true'), $this));
	}

	public function isFalse($failMessage = null)
	{
		return $this->isEqualTo(false, $failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not false'), $this));
	}

	protected static function check($variable, $method)
	{
		if (self::isBoolean($variable) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be a boolean');
		}
	}

	protected static function isBoolean($variable)
	{
		return (is_bool($variable) === true);
	}
}

?>
