<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class dom extends asserters\object
{
	public function __get($property)
	{
		switch ($property)
		{
			case 'toString':
				return $this->toString();

			default:
				return parent::__get($property);
		}
	}

	public function setWith($value, $checkType = true)
	{
		parent::setWith($value, false);

		if ($checkType === true)
		{
			if (self::isDom($this->value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not a DOM node'), $this));
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
		if (($actual = sizeof($this->valueIsSet()->value->childNodes)) == $size)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has size %d, expected size %d'), $this, $actual, $size));
		}

		return $this;
	}

	public function isCloneOf($object, $failMessage = null)
	{
		if ($failMessage === null)
		{
			$failMessage = sprintf($this->getLocale()->_('%s is not a clone of %s'), $this, $this->getTypeOf($object));
		}

		return $this->isEqualTo($object, $failMessage)->isNotIdenticalTo($object, $failMessage);
	}

	public function isEmpty($failMessage = null)
	{
		if ($this->valueIsSet()->value->firstChild === null)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not empty'), $this));
		}

		return $this;
	}

	public function isNotEmpty($failMessage = null)
	{
		if ($this->valueIsSet()->value->firstChild !== null)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is empty'), $this));
		}

		return $this;
	}

	public function isEqualTo($expected, $failMessage = null)
	{
		if ($this->valueIsSet()->value->C14N() === $expected->C14N())
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: sprintf($this->getLocale()->_('%s is not equal to %s'), $this, $this->getTypeOf($expected)));
		}

		return $this;
	}

	public function isNotEqualTo($expected, $failMessage = null)
	{
		if ($this->valueIsSet()->value->C14N() !== $expected->C14N())
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: sprintf($this->getLocale()->_('%s is equal to %s'), $this, $this->getTypeOf($expected)));
		}

		return $this;
	}

	public function toString()
	{
		return $this->generator->castToString($this->valueIsSet()->value);
	}

	protected function valueIsSet($message = null)
	{
		$message = $message ?: 'DOM node is undefined';

		if (self::isDom(parent::valueIsSet($message)->value) === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}

	protected static function isDom($value)
	{
		return ($value instanceof \DOMNode);
	}

	protected static function classExists($value)
	{
		return (class_exists($value) === true || interface_exists($value) === true);
	}
}
