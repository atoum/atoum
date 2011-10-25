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
class integer extends asserters\variable
{
	public function setWith($value, $label = null)
	{
		parent::setWith($value, $label);

		if (self::isInteger($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not an integer'), $this));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function isZero($failMessage = null)
	{
		return $this->isEqualTo(0, $failMessage);
	}

	public function isEqualTo($value, $failMessage = null)
	{
		static::check($value, __METHOD__);

		return parent::isEqualTo($value, $failMessage);
	}

	public function isGreaterThan($value, $failMessage = null)
	{
		self::check($value, __METHOD__);

		if ($this->value > $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not greater than %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isLowerThan($value, $failMessage = null)
	{
		self::check($value, __METHOD__);

		if ($this->value < $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not lower than %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isGreaterThanOrEqualTo($value, $failMessage = null)
	{
		self::check($value, __METHOD__);

		if ($this->value >= $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not greater than or equal to %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isLowerThanOrEqualTo($value, $failMessage = null)
	{
		self::check($value, __METHOD__);

		if ($this->value <= $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not lower than or equal to %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	protected static function check($value, $method)
	{
		if (self::isInteger($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be an integer');
		}
	}

	protected static function isInteger($value)
	{
		return (is_integer($value) === true);
	}
}

?>
