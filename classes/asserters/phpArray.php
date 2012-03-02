<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
;

class phpArray extends asserters\variable
{
	protected $key = null;

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

	public function getKey()
	{
		return $this->key;
	}

	public function atKey($key, $failMessage = null)
	{
		$this->valueIsSet()->key = $key;

		if (isset($this->value[$this->key]) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has no key %s'), $this, $this->getTypeOf($this->key)));
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
        return $this->containsCommon($value, $failMessage, true);
    }

	public function contains($value, $failMessage = null)
    {
        return $this->containsCommon($value, $failMessage, false);
    }

    public function strictlyNotContains($value, $failMessage = null)
    {
        return $this->notContainsCommon($value, $failMessage, true);
    }

	public function notContains($value, $failMessage = null)
    {
        return $this->notContainsCommon($value, $failMessage, false);
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

    protected function containsCommon($value, $failMessage = null, $strict)
	{
		if ($this->valueIsSet()->key === null)
		{
			if (in_array($value, $this->value, $strict) === true)
			{
				$this->pass();
			}
			else
			{
				$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not contain %s'), $this, $this->getTypeOf($value)));
			}
		}
		else if ($strict ? $this->value[$this->key] === $value : $this->value[$this->key] == $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not contain %s at key %s'), $this, $this->getTypeOf($value), $this->getTypeOf($this->key)));
		}

		return $this;
	}

    protected function notContainsCommon($value, $failMessage = null, $strict)
	{
		if ($this->valueIsSet()->key === null)
		{
			if (in_array($value, $this->value, $strict) === false)
			{
				$this->pass();
			}
			else
			{
				$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s contains %s'), $this, $this->getTypeOf($value)));
			}
		}
		else if ($strict ? $this->value[$this->key] !== $value : $this->value[$this->key] != $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s contains %s at key %s'), $this, $this->getTypeOf($value), $this->getTypeOf($this->key)));
		}

		return $this;
	}

	protected static function isArray($value)
	{
		return (is_array($value) === true);
	}
}

?>
