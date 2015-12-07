<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class iterator extends asserters\object
{
	public function setWith($value, $checkType = true)
	{
		parent::setWith($value, $checkType);

		if ($checkType === true)
		{
			if (self::isIterator($this->value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not an iterator'), $this));
			}
			else
			{
				$this->pass();
			}
		}

		return $this;
	}

	public function hasSize($size, $failMessage = null)
	{
		if (($actual = iterator_count($this->valueIsSet()->value)) == $size)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has size %d, expected size %d'), $this, $actual, $size));
		}

		return $this;
	}

	public function isEmpty($failMessage = null)
	{
		if (($actual = iterator_count($this->valueIsSet()->value)) == 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has size %d'), $this, $actual));
		}

		return $this;
	}

	protected static function isIterator($value)
	{
		return ($value instanceof \iterator);
	}
}
