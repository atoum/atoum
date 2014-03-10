<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class dateTime extends asserters\object
{
	public function setWith($value, $checkType = true)
	{
		parent::setWith($value, false);

		if ($checkType === true)
		{
			if ($this->value instanceof \dateTime)
			{
				$this->pass();
			}
			else
			{
				$this->fail($this->_('%s is not an instance of \\dateTime', $this));
			}
		}

		return $this;
	}

	public function hasTimezone(\dateTimezone $timezone, $failMessage = null)
	{
		if ($this->valueIsSet()->value->getTimezone()->getName() == $timezone->getName())
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Timezone is %s instead of %s', $this->value->getTimezone()->getName(), $timezone->getName()));
		}

		return $this;
	}

	public function hasYear($year, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('Y') === sprintf('%04d', $year))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Year is %s instead of %s', $this->value->format('Y'), $year));
		}

		return $this;
	}

	public function isInYear()
	{
		die('The method ' . __METHOD__ . ' is deprecated, please use ' . __CLASS__ . '::hasYear instead');
	}

	public function hasMonth($month, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('m') === sprintf('%02d', $month))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Month is %s instead of %02d', $this->value->format('m'), $month));
		}

		return $this;
	}

	public function isInMonth()
	{
		die('The method ' . __METHOD__ . ' is deprecated, please use ' . __CLASS__ . '::hasMonth instead');
	}

	public function hasDay($day, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('d') === sprintf('%02d', $day))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Day is %s instead of %02d', $this->value->format('d'), $day));
		}

		return $this;
	}

	public function isInDay()
	{
		die('The method ' . __METHOD__ . ' is deprecated, please use ' . __CLASS__ . '::hasDay instead');
	}

	public function hasDate($year, $month, $day, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('Y-m-d') === sprintf('%04d-%02d-%02d', $year, $month, $day))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Date is %s instead of %s', $this->value->format('Y-m-d'), sprintf('%04d-%02d-%02d', $year, $month, $day)));
		}

		return $this;
	}

	public function hasHours($hours, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('H') === sprintf('%02d', $hours))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Hours are %s instead of %02d', $this->value->format('H'), $hours));
		}

		return $this;
	}

	public function hasMinutes($minutes, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('i') === sprintf('%02d', $minutes))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Minutes are %s instead of %02d', $this->value->format('i'), $minutes));
		}

		return $this;
	}

	public function hasSeconds($seconds, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('s') === sprintf('%02d', $seconds))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Seconds are %s instead of %02d', $this->value->format('s'), $seconds));
		}

		return $this;
	}

	public function hasTime($hours, $minutes, $seconds, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('H:i:s') === sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Time is %s instead of %s', $this->value->format('H:i:s'), sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds)));
		}

		return $this;
	}

	public function hasDateAndTime($year, $month, $day, $hours, $minutes, $seconds, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('Y-m-d H:i:s') === sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hours, $minutes, $seconds))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('Datetime is %s instead of %s', $this->value->format('Y-m-d H:i:s'), sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hours, $minutes, $seconds)));
		}

		return $this;
	}

	protected function valueIsSet($message = 'Instance of \dateTime is undefined')
	{
		if (parent::valueIsSet($message)->value instanceof \dateTime === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}
}
