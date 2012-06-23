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
		static::check($value, __METHOD__);

		return parent::isEqualTo($value, $failMessage);
	}

	public function isGreaterThan($value, $failMessage = null)
	{
		self::check($value, __METHOD__);

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
		self::check($value, __METHOD__);

		if ($this->value < $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not lower than %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	/**
	 * @obsolete use isLessThan instead
	 */
	public function isLowerThan($value, $failMessage = null)
	{
		return $this->isLessThan($value, $failMessage);
	}

	public function isGreaterThanOrEqualTo($value, $failMessage = null)
	{
		self::check($value, __METHOD__);

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

	/**
	 * @obsolete use isLessThanOrEqualTo instead
	 */
	public function isLowerThanOrEqualTo($value, $failMessage = null)
	{
		return $this->isLessThanOrEqualTo($value, $failMessage);
	}

	public function isLessThanOrEqualTo($value, $failMessage = null)
	{
		self::check($value, __METHOD__);

		if ($this->value <= $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not lower than or equal to %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	protected static function check($value, $method)
	{
		if (self::isInteger($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be an integer');
		}
	}

	protected static function isInteger($value)
	{
		return (is_integer($value) === true);
	}
}
