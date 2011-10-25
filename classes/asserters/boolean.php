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
class boolean extends asserters\variable
{
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
