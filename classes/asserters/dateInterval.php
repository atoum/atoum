<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\exceptions
;

class dateInterval extends object
{
	public function __toString()
	{
		return (static::isDateInterval($this->value) === false ? parent::__toString() : $this->format($this->value));
	}

	public function __get($asserter)
	{
		switch (strtolower($asserter))
		{
			case 'iszero':
				return $this->{$asserter}();

			default:
				return parent::__get($asserter);
		}
	}

	public function setWith($value, $checkType = true)
	{
		parent::setWith($value, false);

		if ($checkType === true)
		{
			if (self::isDateInterval($this->value) === true)
			{
				$this->pass();
			}
			else
			{
				$this->fail($this->_('%s is not an instance of \\dateInterval', $this));
			}
		}

		return $this;
	}

	public function isGreaterThan(\dateInterval $interval, $failMessage = null)
	{
		list($date1, $date2) = $this->getDates($interval);

		if ($date1 > $date2)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Interval %s is not greater than %s', $this, $this->format($interval)));
		}

		return $this;
	}

	public function isGreaterThanOrEqualTo(\dateInterval $interval, $failMessage = null)
	{
		list($date1, $date2) = $this->getDates($interval);

		if ($date1 >= $date2)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Interval %s is not greater than or equal to %s', $this, $this->format($interval)));
		}

		return $this;
	}

	public function isLessThan(\dateInterval $interval, $failMessage = null)
	{
		list($date1, $date2) = $this->getDates($interval);

		if ($date1 < $date2)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Interval %s is not less than %s', $this, $this->format($interval)));
		}

		return $this;
	}

	public function isLessThanOrEqualTo(\dateInterval $interval, $failMessage = null)
	{
		list($date1, $date2) = $this->getDates($interval);

		if ($date1 <= $date2)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Interval %s is not less than or equal to %s', $this, $this->format($interval)));
		}

		return $this;
	}

	public function isEqualTo($interval, $failMessage = null)
	{
		list($date1, $date2) = $this->getDates($interval);

		if ($date1 == $date2)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Interval %s is not equal to %s', $this, $this->format($interval)));
		}

		return $this;
	}

	public function isZero($failMessage = null)
	{
		return $this->isEqualTo(new \dateInterval('P0D'), $failMessage ?: $this->_('Interval %s is not equal to zero', $this));
	}

	protected function valueIsSet($message = 'Interval is undefined')
	{
		if (self::isDateInterval(parent::valueIsSet($message)->value) === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}

	protected function getDates(\dateInterval $interval)
	{
		$this->valueIsSet();

		$date1 = new \dateTime();
		$date2 = clone $date1;

		return array($date1->add($this->value), $date2->add($interval));
	}

	protected static function isDateInterval($value)
	{
		return ($value instanceof \dateInterval);
	}

	protected function format(\dateInterval $interval)
	{
		return $interval->format($this->_('%Y/%M/%D %H:%I:%S'));
	}
}
