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

    public function hasKeys (array $values, $failMessage = null)
    {
        $missing = array();
        foreach ($values as $value)
        {
            if (!array_key_exists($value, $this->value))
            {
                $missing[] = $value;
            }
        }
        if (count($missing))
        {
            $this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s should have keys %s'), $this, $this->getTypeOf($missing))));
        }
        else
        {
            $this->pass();
        }
        return $this;
    }

    public function notHasKeys (array $values, $failMessage = null)
    {
        $shouldNotBePresent = array();
        foreach ($values as $value)
        {
            if (array_key_exists($value, $this->value))
            {
                $shouldNotBePresent[] = $value;
            }
        }
        if (count($shouldNotBePresent))
        {
            $this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s should not have keys %s'), $this, $this->getTypeOf($shouldNotBePresent))));
        }
        else
        {
            $this->pass();
        }
        return $this;
    }

    public function hasKey ($value, $failMessage = null)
    {
        if (array_key_exists($value, $this->value))
        {
            $this->pass();
        }
        else
        {
            $this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has no key %s'), $this, $this->getTypeOf($value))));
        }
        return $this;
    }

    public function notHasKey ($value, $failMessage = null)
    {
        if (!array_key_exists($value, $this->value))
        {
            $this->pass();
        }
        else
        {
            $this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has a key %s'), $this, $this->getTypeOf($value))));
        }
        return $this;
    }

    public function containsValues (array $values, $failMessage = null)
    {
        return $this->containsValuesCommon($values, $failMessage, false);
    }

    public function strictlyContainsValues (array $values, $failMessage = null)
    {
        return $this->containsValuesCommon($values, $failMessage, true);
    }

    public function notContainsValues (array $values, $failMessage = null)
    {
        return $this->notContainsValuesCommon($values, $failMessage, false);
    }

    public function strictlyNotContainsValues (array $values, $failMessage = null)
    {
        return $this->notContainsValuesCommon($values, $failMessage, true);
    }

    protected function containsValuesCommon ($values, $failMessage, $strict)
    {
        $missing = array();
        foreach ($values as $value)
        {
            if (!in_array($value, $this->value, $strict))
            {
                $missing[] = $value;
            }
        }
        if (count($missing))
        {
            $this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not contain values %s'), $this, $this->getTypeOf($missing))));
        }
        else
        {
            $this->pass();
        }
        return $this;
    }

    protected function notContainsValuesCommon ($values, $failMessage, $strict)
    {
        $shouldNotBePresent = array();
        foreach ($values as $value)
        {
            if (in_array($value, $this->value, $strict))
            {
                $shouldNotBePresent[] = $value;
            }
        }
        if (count($shouldNotBePresent))
        {
            $this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s should not contain values %s'), $this, $this->getTypeOf($shouldNotBePresent))));
        }
        else
        {
            $this->pass();
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

    protected function containsCommon($value, $failMessage = null, $strict)
	{
        if (in_array($value, $this->valueIsSet()->value, $strict) === true)
        {
            $this->pass();
        }
        else
        {
            $this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not contain %s'), $this, $this->getTypeOf($value)));
        }
		return $this;
	}

    protected function notContainsCommon($value, $failMessage = null, $strict)
	{
        if (in_array($value, $this->valueIsSet()->value, $strict) === false)
        {
            $this->pass();
        }
        else
        {
            $this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s contains %s'), $this, $this->getTypeOf($value)));
        }
		return $this;
	}

	protected static function isArray($value)
	{
		return (is_array($value) === true);
	}
}

?>