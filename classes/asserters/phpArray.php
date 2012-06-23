<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
;

class phpArray extends asserters\variable
{
	public function setWith($value, $label = null)
	{
		parent::setWith($value, $label);

		if (self::isArray($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not an array'), $this));
		}
		else
		{
			$this->pass();
		}

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
			$diff = new diffs\variable();

			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not empty'), $this)));
		}

		return $this;
	}

	public function isNotEmpty($failMessage = null)
	{
		if (sizeof($this->valueIsSet()->value) > 0)
		{
			$this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is empty'), $this)));
		}

		return $this;
	}

	public function strictlyContains($value, $failMessage = null)
	{
		return $this->containsValue($value, $failMessage, true);
	}

	public function contains($value, $failMessage = null)
	{
		return $this->containsValue($value, $failMessage, false);
	}

	public function strictlyNotContains($value, $failMessage = null)
	{
		return $this->notContainsValue($value, $failMessage, true);
	}

	public function notContains($value, $failMessage = null)
	{
		return $this->notContainsValue($value, $failMessage, false);
	}

	public function hasKeys(array $keys, $failMessage = null)
	{
		if (sizeof($undefinedKeys = array_diff($keys, array_keys($this->value))) <= 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s should have keys %s'), $this, $this->getTypeOf($undefinedKeys))));
		}

		return $this;
	}

	public function notHasKeys(array $keys, $failMessage = null)
	{
		if (sizeof($definedKeys = array_intersect(array_keys($this->value), $keys)) <= 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s should not have keys %s'), $this, $this->getTypeOf($definedKeys))));
		}

		return $this;
	}

	public function hasKey($key, $failMessage = null)
	{
		if (array_key_exists($key, $this->value))
		{
			$this->pass();
		}
		else
		{
			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has no key %s'), $this, $this->getTypeOf($key))));
		}

		return $this;
	}

	public function notHasKey($key, $failMessage = null)
	{
		if (array_key_exists($key, $this->value) === false)
		{
			$this->pass();
		}
		else
		{
			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has a key %s'), $this, $this->getTypeOf($key))));
		}

		return $this;
	}

	public function containsValues(array $values, $failMessage = null)
	{
		return $this->intersect($values, $failMessage, false);
	}

	public function strictlyContainsValues(array $values, $failMessage = null)
	{
		return $this->intersect($values, $failMessage, true);
	}

	public function notContainsValues(array $values, $failMessage = null)
	{
		return $this->notIntersect($values, $failMessage, false);
	}

	public function strictlyNotContainsValues(array $values, $failMessage = null)
	{
		return $this->notIntersect($values, $failMessage, true);
	}

	protected function containsValue($value, $failMessage, $strict)
	{
		if (in_array($value, $this->valueIsSet()->value, $strict) === true)
		{
			$this->pass();
		}
		else if ($strict === false)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not contain %s'), $this, $this->getTypeOf($value)));
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not strictly contain %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	protected function notContainsValue($value, $failMessage, $strict)
	{
		if (in_array($value, $this->valueIsSet()->value, $strict) === false)
		{
			$this->pass();
		}
		else if ($strict === false)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s contains %s'), $this, $this->getTypeOf($value)));
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s contains strictly %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	protected function intersect(array $values, $failMessage, $strict)
	{
		$unknownValues = array();

		foreach ($values as $value) if (in_array($value, $this->value, $strict) === false)
		{
			$unknownValues[] = $value;
		}

		if (sizeof($unknownValues) <= 0)
		{
			$this->pass();
		}
		else if ($strict === false)
		{
			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not contain values %s'), $this, $this->getTypeOf($unknownValues))));
		}
		else
		{
			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not contain strictly values %s'), $this, $this->getTypeOf($unknownValues))));
		}

		return $this;
	}

	protected function notIntersect(array $values, $failMessage, $strict)
	{
		$knownValues = array();

		foreach ($values as $value) if (in_array($value, $this->value, $strict) === true)
		{
			$knownValues[] = $value;
		}

		if (sizeof($knownValues) <= 0)
		{
			$this->pass();
		}
		else if ($strict === false)
		{
			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s should not contain values %s'), $this, $this->getTypeOf($knownValues))));
		}
		else
		{
			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s should not contain strictly values %s'), $this, $this->getTypeOf($knownValues))));
		}

		return $this;
	}

	protected function valueIsSet($message = 'Array is undefined')
	{
		return parent::valueIsSet($message);
	}

	protected static function check($value, $method)
	{
		if (self::isArray($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be an array');
		}
	}

	protected static function isArray($value)
	{
		return (is_array($value) === true);
	}
}
