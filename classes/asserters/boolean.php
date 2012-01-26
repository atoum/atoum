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
class boolean extends asserters\variable
{
	public function __get($property)
	{
		switch (true)
		{
			case strtolower($property) == 'isfalse':
				return $this->isFalse();

			case strtolower($property) == 'istrue':
				return $this->isTrue();

			default:
				return parent::__get($property);
		}
	}

	public function setWith($value, $label = null)
	{
		parent::setWith($value, $label);

		if (self::isBoolean($this->value) === false)
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

	protected static function check($value, $method)
	{
		if (self::isBoolean($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be a boolean');
		}
	}

	protected static function isBoolean($value)
	{
		return (is_bool($value) === true);
	}
}

?>
