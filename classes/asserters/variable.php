<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
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
class variable extends atoum\asserter
{
	protected $isSet = false;
	protected $value = null;
	protected $isSetByReference = false;

	public function __toString()
	{
		return $this->getTypeOf($this->value);
	}

	public function wasSet()
	{
		return ($this->isSet === true);
	}

	public function setWith($value)
	{
		$this->value = $value;
		$this->isSet = true;
		$this->isSetByReference = false;

		return $this;
	}

	public function setByReferenceWith(& $value)
	{
		$this->value = & $value;
		$this->isSet = true;
		$this->isSetByReference = true;

		return $this;
	}

	public function reset()
	{
		$this->value = null;
		$this->isSet = false;
		$this->isSetByReference = false;

		return $this;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function isSetByReference()
	{
		return ($this->isSet === true && $this->isSetByReference === true);
	}

	public function isEqualTo($value, $failMessage = null)
	{
		self::check($value, __METHOD__);

		if ($this->valueIsSet()->value == $value)
		{
			$this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(
				($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not equal to %s'), $this, $this->getTypeOf($value))) .
				PHP_EOL .
				$diff->setReference($value)->setData($this->value)
			);
		}

		return $this;
	}

	public function isNotEqualTo($value, $failMessage = null)
	{
		self::check($value, __METHOD__);

		if ($this->valueIsSet()->value != $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is equal to %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isIdenticalTo($value, $failMessage = null)
	{
		self::check($value, __METHOD__);

		if ($this->valueIsSet()->value === $value)
		{
			$this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(
				($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not identical to %s'), $this, $this->getTypeOf($value))) .
				PHP_EOL .
				$diff->setReference($value)->setData($this->value)
			);
		}

		return $this;
	}

	public function isNotIdenticalTo($value, $failMessage = null)
	{
		self::check($value, __METHOD__);

		if ($this->valueIsSet()->value !== $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is identical to %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isNull($failMessage = null)
	{
		if ($this->valueIsSet()->value === null)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not null'), $this));
		}

		return $this;
	}

	public function isNotNull($failMessage = null)
	{
		if ($this->valueIsSet()->value !== null)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is null'), $this));
		}

		return $this;
	}

	public function isReferenceTo(& $reference, $failMessage = null)
	{
		if ($this->valueIsSet()->isSetByReference() === false)
		{
			throw new exceptions\logic('Value is not set by reference');
		}

		if (is_object($this->value) === true && is_object($reference) === true)
		{
			if ($this->value === $reference)
			{
				$this->pass();
			}
			else
			{
				$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not a reference to %s'), $this, $this->getTypeOf($reference)));
			}
		}
		else
		{
			$tmp = $reference;
			$reference = uniqid(mt_rand());
			$isReference = ($this->value === $reference);
			$reference = $tmp;

			if ($isReference === true)
			{
				$this->pass();
			}
			else
			{
				$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not a reference to %s'), $this, $this->getTypeOf($reference)));
			}
		}

		return $this;
	}

	protected function valueIsSet($message = 'Value is undefined')
	{
		if ($this->isSet === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}

	protected static function check($value, $method) {}
}

?>
