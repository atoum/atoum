<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
;

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

	public function isCallable($failMessage = null)
	{
		if (is_callable($this->valueIsSet()->value) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not callable'), $this));
		}

		return $this;
	}

	public function isNotCallable($failMessage = null)
	{
		if (is_callable($this->valueIsSet()->value) === false)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is callable'), $this));
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
