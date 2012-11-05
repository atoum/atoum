<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class integer extends asserters\variable
{
	public function setWith($value, $label = null)
	{
		parent::setWith($value, $label);

		if (self::isInteger($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not an integer'), $this));
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

	public function isEqualTo($value, $failMessage = null)
	{
		static::check($value, __FUNCTION__);

		return parent::isEqualTo($value, $failMessage);
	}

	public function isGreaterThan($value, $failMessage = null)
	{
		static::check($value, __FUNCTION__);

		if ($this->value > $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not greater than %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isLessThan($value, $failMessage = null)
	{
		static::check($value, __FUNCTION__);

		if ($this->value < $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not less than %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isGreaterThanOrEqualTo($value, $failMessage = null)
	{
		static::check($value, __FUNCTION__);

		if ($this->value >= $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not greater than or equal to %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isLessThanOrEqualTo($value, $failMessage = null)
	{
		static::check($value, __FUNCTION__);

		if ($this->value <= $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not less than or equal to %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	protected static function check($value, $method)
	{
		if (self::isInteger($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . __CLASS__ . '::' . $method . '() must be an integer');
		}
	}

	protected static function isInteger($value)
	{
		return (is_integer($value) === true);
	}
}
