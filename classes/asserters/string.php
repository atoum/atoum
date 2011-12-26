<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\exceptions
;

/**
 * @property    \mageekguy\atoum\asserter                       if
 * @property    \mageekguy\atoum\asserter                       and
 * @property    \mageekguy\atoum\asserter                       then
 *
 * @method      \mageekguy\atoum\asserter                       if()
 * @method      \mageekguy\atoum\asserter                       and()
 * @method      \mageekguy\atoum\asserter                       then()
 *
 * @method      \mageekguy\atoum\asserters\adapter              adapter()
 * @method      \mageekguy\atoum\asserters\afterDestructionOf   afterDestructionOf()
 * @method      \mageekguy\atoum\asserters\phpArray             array()
 * @method      \mageekguy\atoum\asserters\boolean              boolean()
 * @method      \mageekguy\atoum\asserters\castToString         castToString()
 * @method      \mageekguy\atoum\asserters\phpClass             class()
 * @method      \mageekguy\atoum\asserters\dateTime             dateTime()
 * @method      \mageekguy\atoum\asserters\error                error()
 * @method      \mageekguy\atoum\asserters\exception            exception()
 * @method      \mageekguy\atoum\asserters\float                float()
 * @method      \mageekguy\atoum\asserters\hash                 hash()
 * @method      \mageekguy\atoum\asserters\integer              integer()
 * @method      \mageekguy\atoum\asserters\mock                 mock()
 * @method      \mageekguy\atoum\asserters\mysqlDateTime        mysqlDateTime()
 * @method      \mageekguy\atoum\asserters\object               object()
 * @method      \mageekguy\atoum\asserters\output               output()
 * @method      \mageekguy\atoum\asserters\phpArray             phpArray()
 * @method      \mageekguy\atoum\asserters\phpClass             phpClass()
 * @method      \mageekguy\atoum\asserters\sizeOf               sizeOf()
 * @method      \mageekguy\atoum\asserters\stream               stream()
 * @method      \mageekguy\atoum\asserters\string               string()
 * @method      \mageekguy\atoum\asserters\testedClass          testedClass()
 * @method      \mageekguy\atoum\asserters\variable             variable()
 */
class string extends variable
{
	protected $charlist = null;

	public function __toString()
	{
		return (is_string($this->value) === false ? parent::__toString() : sprintf($this->getLocale()->_('string(%s) \'%s\''), strlen($this->value), addcslashes($this->value, $this->charlist)));
	}


	public function getCharlist()
	{
		return $this->charlist;
	}

	public function setWith($value, $label = null, $charlist = null, $checkType = true)
	{
		parent::setWith($value, $label);

		$this->charlist = $charlist;

		if ($checkType === true)
		{
			if (self::isString($this->value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not a string'), $this));
			}
			else
			{
				$this->pass();
			}
		}

		return $this;
	}

	public function isEmpty($failMessage = null)
	{
		return $this->isEqualTo('', $failMessage);
	}

	public function isNotEmpty($failMessage = null)
	{
		return $this->isNotEqualTo('', $failMessage !== null ? $failMessage : $this->getLocale()->_('string is empty'));
	}

	public function match($pattern, $failMessage = null)
	{
		if (preg_match($pattern, $this->valueIsSet()->value) === 1)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not match %s'), $this, $pattern));
		}

		return $this;
	}

	public function isEqualTo($value, $failMessage = null)
	{
		return parent::isEqualTo($value, $failMessage !== null ? $failMessage : $this->getLocale()->_('strings are not equals'));
	}

	public function hasLength($length, $failMessage = null)
	{
		if (strlen($this->valueIsSet()->value) == $length)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('length of %s is not %d'), $this, $length));
		}

		return $this;
	}

	protected static function check($value, $method)
	{
		if (self::isString($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be a string');
		}
	}

	protected static function isString($value)
	{
		return (is_string($value) === true);
	}
}

?>
