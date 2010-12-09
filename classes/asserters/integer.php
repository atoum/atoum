<?php

namespace mageekguy\atoum\asserters;

use \mageekguy\atoum\exceptions;

class integer extends \mageekguy\atoum\asserters\variable
{
	public function setWith($variable)
	{
		parent::setWith($variable);

		if (self::isInteger($this->variable) === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not an integer'), $this));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function isZero($failMessage = null)
	{
		return $this->isEqualTo(0, $failMessage);
	}

	public function isEqualTo($variable, $failMessage = null)
	{
		static::check($variable, __METHOD__);

		return parent::isEqualTo($variable, $failMessage);
	}

	public function isGreaterThan($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		if ($this->variable > $variable)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not greater than  %s'), $this, $this->toString($variable)));
		}
	}

	protected static function check($variable, $method)
	{
		if (self::isInteger($variable) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be an integer');
		}
	}

	protected static function isInteger($variable)
	{
		return (is_integer($variable) === true);
	}
}

?>
