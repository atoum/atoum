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
		switch (strtolower($property))
		{
			case 'tostring':
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

	public function isEqualTo($value, $failMessage = null)
	{
		$object = $this->valueIsSet()->value;

		$identities = array(self::getIdentity($value));
		$actual = self::removeRecursion($object, $identities);

		$identities = array(self::getIdentity($object));
		$expected = self::removeRecursion($value, $identities);

		$this->setWith($actual);
		$result = parent::isEqualTo($expected, $failMessage);
		$this->setWith($object);

		return $result;
	}

	public function toString()
	{
		return $this->generator->castToString($this->valueIsSet()->value);
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

	protected static function removeRecursion($mixed, & $identities = array(), & $recusrsion = false)
	{
		if (@json_encode($mixed))
		{
			return $mixed;
		}

		if (is_array($mixed))
		{
			return self::cleanClosures($mixed);
		}

		$identity = self::getIdentity($mixed);

		if (in_array($identity, $identities))
		{
			$recusrsion = true;

			return (object) $identity;
		}

		$identities[] = $identity;
		$properties = array();
		$reflection = new \reflectionObject($mixed);

		foreach ($reflection->getProperties() as $property)
		{
			$access = $property->isPublic();
			$property->setAccessible(true);

			$value = $property->getValue($mixed);

			if (is_object($value))
			{
				$value = self::removeRecursion($value, $identities, $recusrsion);
			}

			if (is_array($value))
			{
				$value = self::removeRecursion($value, $identities, $recusrsion);
			}

			$properties[$property->getName()] = $value;

			$property->setAccessible($access);
		}

		if ($recusrsion === false)
		{
			return $mixed;
		}

		return (object) $properties;
	}

	protected static function getIdentity($mixed)
	{
		return md5(serialize(self::cleanClosures($mixed)));
	}

	protected static function cleanClosures($mixed)
	{
		$values = array();

		if (is_object($mixed))
		{
			$reflection = new \reflectionObject($mixed);

			foreach ($reflection->getProperties() as $property)
			{
				$access = $property->isPublic();
				$property->setAccessible(true);

				$value = $property->getValue($mixed);

				$values[] = $value;

				$property->setAccessible($access);
			}
		}
		else
		{
			$values = $mixed;
		}

		return array_map(
			function($value) use (& $identity)
			{
				if (is_array($value))
				{
					return self::cleanClosures($value);
				}

				if ($value instanceof \closure)
				{
					return spl_object_hash($value);
				}

				return $value;
			},
			$values
		);
	}
}
