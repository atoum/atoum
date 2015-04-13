<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class integer extends asserters\variable
{
	public function __get($property)
	{
		switch (strtolower($property))
		{
			case 'iszero':
				return $this->isZero();

			default:
				return parent::__get($property);
		}
	}

	public function setWith($value)
	{
		parent::setWith($value);

		if ($this->analyzer->isInteger($this->value) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($this->_('%s is not an integer', $this));
		}

		return $this;
	}

	public function isZero($failMessage = null)
	{
		return $this->isEqualTo(0, $failMessage);
	}

	public function isGreaterThan($value, $failMessage = null)
	{
		if ($this->valueIsSet()->value > $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not greater than %s', $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isGreaterThanOrEqualTo($value, $failMessage = null)
	{
		if ($this->valueIsSet()->value >= $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not greater than or equal to %s', $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isLessThan($value, $failMessage = null)
	{
		if ($this->valueIsSet()->value < $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not less than %s', $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isLessThanOrEqualTo($value, $failMessage = null)
	{
		if ($this->valueIsSet()->value <= $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not less than or equal to %s', $this, $this->getTypeOf($value)));
		}

		return $this;
	}
}
