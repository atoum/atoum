<?php

namespace mageekguy\atoum\asserters;

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

	public function isZero()
	{
		return $this->isEqualTo(0);
	}

	public function isEqualTo($integer, $failMessage = null)
	{
		if (self::isInteger($integer) === false)
		{
			throw new \logicException('Argument must be an integer');
		}

		return parent::isEqualTo($integer, $failMessage);
	}

	public function isGreaterThan($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		$this->variable > $variable ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not greater than  %s'), $this, self::toString($variable)));

		return $this;
	}

	protected function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new \logicException('Argument must be set at index 0');
		}

		return $this->setWith($arguments[0]);
	}

	protected static function check($variable, $method)
	{
		if (self::isInteger($variable) === false)
		{
			throw new \logicException('Argument of ' . $method . '() must be an integer');
		}
	}

	protected static function isInteger($variable)
	{
		return (is_integer($variable) === true);
	}
}

?>
