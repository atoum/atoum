<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
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
class afterDestructionOf extends atoum\asserter
{
	public function setWith($value)
	{
		if (is_object($value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not an object'), $this->getTypeOf($value)));
		}
		else if (method_exists($value, '__destruct') === false)
		{
			$this->fail(sprintf($this->getLocale()->_('Destructor of class %s is undefined'), get_class($value)));
		}
		else
		{
			$value->__destruct();

			$this->pass();
		}

		return $this;
	}
}

?>
