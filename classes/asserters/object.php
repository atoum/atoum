<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class object extends asserters\variable
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
		parent::setWith($value);

		if ($checkType === true)
		{
			if (self::isObject($this->value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not an object'), $this));
			}
			else
			{
				$this->pass();
			}
		}

		return $this;
	}

	public function isInstanceOf($value)
	{
		try
		{
			self::check($value, __METHOD__);
		}
		catch (\logicException $exception)
		{
			if (self::classExists($value) === false)
			{
				throw new exceptions\logic('Argument of ' . __METHOD__ . '() must be a class instance or a class name');
			}
		}

		$this->valueIsSet()->value instanceof $value ? $this->pass() : $this->fail(sprintf($this->getLocale()->_('%s is not an instance of %s'), $this, is_string($value) === true ? $value : $this->getTypeOf($value)));

		return $this;
	}

	public function hasSize($size, $failMessage = null)
	{
		if (sizeof($this->valueIsSet()->value) == $size)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has not size %d'), $this, $size));
		}

		return $this;
	}

	public function isEmpty($failMessage = null)
	{
		if (sizeof($this->valueIsSet()->value) == 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has size %d'), $this, sizeof($this->value)));
		}

		return $this;
	}

	public function toString()
	{
		return $this->generator->castToString($this->valueIsSet()->value);
	}

	protected function valueIsSet($message = 'Object is undefined')
	{
		if (self::isObject(parent::valueIsSet($message)->value) === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}

	protected static function check($value, $method)
	{
		if (self::isObject($value) === false)
		{
			throw new exceptions\logic('Argument of ' . $method . '() must be a class instance');
		}
	}

	protected static function isObject($value)
	{
		return (is_object($value) === true);
	}

	protected static function classExists($value)
	{
		return (class_exists($value) === true || interface_exists($value) === true);
	}
}
