<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

/**
 * @property    mageekguyatoum\asserter                       if
 * @property    mageekguyatoum\asserter                       and
 * @property    mageekguyatoum\asserter                       then
 *
 * @method      mageekguyatoum\asserter                       if()
 * @method      mageekguyatoum\asserter                       and()
 * @method      mageekguyatoum\asserter                       then()
 *
 * @method      mageekguyatoum\asserters\adapter              adapter()
 * @method      mageekguyatoum\asserters\afterDestructionOf   afterDestructionOf()
 * @method      mageekguyatoum\asserters\phpArray             array()
 * @method      mageekguyatoum\asserters\boolean              boolean()
 * @method      mageekguyatoum\asserters\castToString         castToString()
 * @method      mageekguyatoum\asserters\phpClass             class()
 * @method      mageekguyatoum\asserters\dateTime             dateTime()
 * @method      mageekguyatoum\asserters\error                error()
 * @method      mageekguyatoum\asserters\exception            exception()
 * @method      mageekguyatoum\asserters\float                float()
 * @method      mageekguyatoum\asserters\hash                 hash()
 * @method      mageekguyatoum\asserters\integer              integer()
 * @method      mageekguyatoum\asserters\mock                 mock()
 * @method      mageekguyatoum\asserters\mysqlDateTime        mysqlDateTime()
 * @method      mageekguyatoum\asserters\object               object()
 * @method      mageekguyatoum\asserters\output               output()
 * @method      mageekguyatoum\asserters\phpArray             phpArray()
 * @method      mageekguyatoum\asserters\phpClass             phpClass()
 * @method      mageekguyatoum\asserters\sizeOf               sizeOf()
 * @method      mageekguyatoum\asserters\stream               stream()
 * @method      mageekguyatoum\asserters\string               string()
 * @method      mageekguyatoum\asserters\testedClass          testedClass()
 * @method      mageekguyatoum\asserters\variable             variable()
 */
class dateTime extends asserters\object
{
	public function setWith($value, $checkType = true)
	{
		parent::setWith($value, false);

		if ($checkType === true)
		{
			if (self::isDateTime($this->value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not an instance of \\dateTime'), $this));
			}
			else
			{
				$this->pass();
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
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('Timezone is %s instead of %s'), $this->value->getTimezone()->getName(), $timezone->getName()));
		}

		return $this;
	}

	public function isInYear($year, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('Y') == $year)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('Year is %s instead of %s'), $year, $this->value->format('Y')));
		}

		return $this;
	}

	public function isInMonth($month, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('n') == $month)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('Month is %s instead of %s'), $month, $this->value->format('n')));
		}

		return $this;
	}

	public function isInDay($day, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('j') == $day)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('Day is %s instead of %s'), $day, $this->value->format('j')));
		}

		return $this;
	}

	public function hasDate($year, $month = null, $day = null, $failMessage = null)
	{
		if ($this->valueIsSet()->value->format('Y-m-d') == sprintf('%d-%02d-%02d', $year, $month, $day))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('Date is %s instead of %s'), sprintf('%d-%02d-%02d', $year, $month, $day), $this->value->format('Y-m-d')));
		}

		return $this;
	}

	protected function valueIsSet($message = 'Instance of \dateTime is undefined')
	{
		if (self::isDateTime(parent::valueIsSet($message)->value) === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}

	protected static function check($value, $method)
	{
		if (self::isDateTime($value) === false)
		{
			throw new exceptions\logic('Argument of ' . $method . '() must be an instance of \\dateTime');
		}
	}

	protected static function isDateTime($value)
	{
		return ($value instanceof \dateTime);
	}
}

?>
