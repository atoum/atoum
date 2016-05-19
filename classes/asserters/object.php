<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\exceptions
;

class object extends variable
{
	public function __get($property)
	{
		switch (strtolower($property))
		{
			case 'tostring':
			case 'toarray':
			case 'isempty':
			case 'istestedinstance':
			case 'isnottestedinstance':
			case 'isinstanceoftestedclass':
				return $this->{$property}();

			default:
				return parent::__get($property);
		}
	}

	public function setWith($value, $checkType = true)
	{
		parent::setWith($value);

		if ($checkType === true)
		{
			if ($this->analyzer->isObject($this->value) === true)
			{
				$this->pass();
			}
			else
			{
				$this->fail($this->_('%s is not an object', $this));
			}
		}

		return $this;
	}

	public function isInstanceOf($value, $failMessage = null)
	{
		try
		{
			self::check($value, __FUNCTION__);
		}
		catch (\logicException $exception)
		{
			if (self::classExists($value) === false)
			{
				throw new exceptions\logic('Argument of ' . __METHOD__ . '() must be a class instance or a class name');
			}
		}

		$this->valueIsSet()->value instanceof $value ? $this->pass() : $this->fail($failMessage ?: $this->_('%s is not an instance of %s', $this, is_string($value) === true ? $value : $this->getTypeOf($value)));

		return $this;
	}

	public function isNotInstanceOf($value, $failMessage = null)
	{
		try
		{
			self::check($value, __FUNCTION__);
		}
		catch (\logicException $exception)
		{
			if (self::classExists($value) === false)
			{
				throw new exceptions\logic('Argument of ' . __METHOD__ . '() must be a class instance or a class name');
			}
		}

		$this->valueIsSet()->value instanceof $value === false ? $this->pass() : $this->fail($failMessage ?: $this->_('%s is an instance of %s', $this, is_string($value) === true ? $value : $this->getTypeOf($value)));

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
			$this->fail($failMessage ?: $this->_('%s has size %d, expected size %d', $this, sizeof($this->valueIsSet()->value), $size));
		}

		return $this;
	}

	public function isCloneOf($object, $failMessage = null)
	{
		if ($failMessage === null)
		{
			$failMessage = $this->_('%s is not a clone of %s', $this, $this->getTypeOf($object));
		}

		return $this->isEqualTo($object, $failMessage)->isNotIdenticalTo($object, $failMessage);
	}

	public function isEmpty($failMessage = null)
	{
		if (sizeof($this->valueIsSet()->value) == 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s has size %d', $this, sizeof($this->value)));
		}

		return $this;
	}

	public function isTestedInstance($failMessage = null)
	{
		return $this->valueIsSet()->testedInstanceIsSet()->isIdenticalTo($this->test->testedInstance, $failMessage);
	}

	public function isNotTestedInstance($failMessage = null)
	{
		return $this->valueIsSet()->testedInstanceIsSet()->isNotIdenticalTo($this->test->testedInstance, $failMessage);
	}

	public function isInstanceOfTestedClass($failMessage = null)
	{
		return $this->valueIsSet()->testedInstanceIsSet()->isInstanceOf($this->test->getTestedClassName(), $failMessage);
	}

	public function toString()
	{
		return $this->generator->castToString($this->valueIsSet()->value);
	}

	public function toArray()
	{
		return $this->generator->castToArray($this->valueIsSet()->value);
	}

	protected function valueIsSet($message = 'Object is undefined')
	{
		if ($this->analyzer->isObject(parent::valueIsSet($message)->value) === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}

	protected function testedInstanceIsSet()
	{
		if ($this->test === null || $this->test->testedInstance === null)
		{
			throw new exceptions\logic('Tested instance is undefined in the test');
		}

		return $this;
	}

	protected function check($value, $method)
	{
		if ($this->analyzer->isObject($value) === false)
		{
			throw new exceptions\logic('Argument of ' . __CLASS__ . '::' . $method . '() must be a class instance');
		}

		return $this;
	}

	protected static function classExists($value)
	{
		return (class_exists($value) === true || interface_exists($value) === true);
	}
}
