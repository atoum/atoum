<?php

namespace mageekguy\atoum\asserters;

class iterator extends object
{
	public function __get($asserter)
	{
		switch (strtolower($asserter))
		{
			case 'size':
				return $this->size();

			case 'isempty':
				return $this->isEmpty();

			case 'isnotempty':
				return $this->isNotEmpty();

			default:
				return parent::__get($asserter);
		}
	}

	public function setWith($value, $checkType = true)
	{
		parent::setWith($value, $checkType);

		if ($checkType === true)
		{
			if (self::isIterator($this->value) === false)
			{
				$this->fail($this->getLocale()->_('%s is not an iterator', $this));
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
			$this->fail($failMessage ?: $this->getLocale()->_('%s has size %d, expected size %d', $this, $actual, $size));
		}

		return $this;
	}

	public function isEmpty($failMessage = null)
	{
		if (($actual = iterator_count($this->valueIsSet()->value)) === 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->getLocale()->_('%s is not empty', $this, $actual));
		}

		return $this;
	}

	public function isNotEmpty($failMessage = null)
	{
		if (iterator_count($this->valueIsSet()->value) > 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is empty', $this));
		}

		return $this;
	}

	protected function size()
	{
		return $this->generator->__call('integer', array(iterator_count($this->valueIsSet()->value)));
	}

	protected static function isIterator($value)
	{
		return ($value instanceof \iterator);
	}
}
