<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

/**
 * @method  mageekguy\atoum\asserters\adapter               adapter()
 * @method  mageekguy\atoum\asserters\afterDestructionOf    afterDestructionOf()
 * @method  mageekguy\atoum\asserters\boolean               boolean()
 * @method  mageekguy\atoum\asserters\castToString          castToString()
 * @method  mageekguy\atoum\asserters\dateTime              dateTime()
 * @method  mageekguy\atoum\asserters\error                 error()
 * @method  mageekguy\atoum\asserters\exception             exception()
 * @method  mageekguy\atoum\asserters\float                 float()
 * @method  mageekguy\atoum\asserters\hash                  hash()
 * @method  mageekguy\atoum\asserters\integer               integer()
 * @method  mageekguy\atoum\asserters\mock                  mock()
 * @method  mageekguy\atoum\asserters\mysqlDateTime         mysqlDateTime()
 * @method  mageekguy\atoum\asserters\object                object()
 * @method  mageekguy\atoum\asserters\output                output()
 * @method  mageekguy\atoum\asserters\phpArray              phpArray()
 * @method  mageekguy\atoum\asserters\phpClass              phpClass()
 * @method  mageekguy\atoum\asserters\sizeOf                sizeOf()
 * @method  mageekguy\atoum\asserters\stream                stream()
 * @method  mageekguy\atoum\asserters\string                string()
 * @method  mageekguy\atoum\asserters\testedClass           testedClass()
 * @method  mageekguy\atoum\asserters\variable              variable()
 */
class mysqlDateTime extends asserters\dateTime
{
	const mysqlDateTimeFormat = 'Y-m-d H:i:s';

	public function setWith($value, $checkType = true)
	{
		$phpDate = \dateTime::createFromFormat(self::mysqlDateTimeFormat, $value);

		if ($phpDate !== false)
		{
			parent::setWith($phpDate, $checkType);
		}
		else
		{
			parent::setWith($value, false);

			if ($checkType === true)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not in format Y-m-d H:i:s'), $this));
			}
		}

		return $this;
	}

	public function getValue()
	{
		$value = parent::getValue();

		return ($value instanceof \dateTime === false ? $value : $value->format(self::mysqlDateTimeFormat));
	}
}

?>
