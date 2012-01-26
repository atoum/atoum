<?php

namespace mageekguy\atoum\asserters;

use
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
class hash extends string
{
	public function isSha1($failMessage = null)
	{
		return $this->isHash(40, $failMessage);
	}

	public function isSha256($failMessage = null)
	{
		return $this->isHash(64, $failMessage);
	}

	public function isSha512($failMessage = null)
	{
		return $this->isHash(128, $failMessage);
	}

	public function isMd5($failMessage = null)
	{
		return $this->isHash(32, $failMessage);
	}

	protected function isHash($length, $failMessage = null)
	{
		if (strlen($this->valueIsSet()->value) === $length)
		{
			 $this->match('/^[a-f0-9]+$/', sprintf($this->getLocale()->_('%s does not match given pattern'), $this));
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s should be a string of %d characters'), $this, $length));
		}

		return $this;
	}
}

?>
