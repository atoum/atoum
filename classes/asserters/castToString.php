<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters
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
class castToString extends asserters\string
{
	public function setWith($value, $label = null, $charlist = null, $checkType = true)
	{
		parent::setWith($value, $label, $charlist, false);

		if ($checkType === true)
		{
			if (self::isObject($value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not an object'), $this->getTypeOf($value)));
			}
			else
			{
				$this->pass();

				$this->value = (string) $this->value;
			}
		}

		return $this;
	}

	protected static function isObject($value)
	{
		return (is_object($value) === true);
	}
}

?>
