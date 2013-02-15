<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
;

class phpArray extends asserters\variable
{
    
    protected $lastContainedValueChecked;
    protected $lastContainedValueKey;
    
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
			$this->fail($failMessage ?: sprintf($this->getLocale()->_('%s has not size %d'), $this, $size));
		}
        $this->getGenerator()->setPreviousAsserter('hasSize');
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
			$this->fail($failMessage ?: sprintf($this->getLocale()->_('%s is not empty'), $this));
		}
        
        $this->getGenerator()->setPreviousAsserter('isEmpty');
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
			$this->fail($failMessage ?: sprintf($this->getLocale()->_('%s is empty'), $this));
		}
        
        $this->getGenerator()->setPreviousAsserter('isNotEmpty');
		return $this;
	}

	public function strictlyContains($value, $failMessage = null)
	{
		$result = $this->containsValue($value, $failMessage, true);
        $this->getGenerator()->setPreviousAsserter('strictlyContains');
        return $result;
	}

	public function contains($value, $failMessage = null)
	{
		$result = $this->containsValue($value, $failMessage, false);
        $this->getGenerator()->setPreviousAsserter('contains');
        return $result;
	}

	public function strictlyNotContains($value, $failMessage = null)
	{
		$result = $this->notContainsValue($value, $failMessage, true);
        $this->getGenerator()->setPreviousAsserter('striclyNotContains');
        return $result;
	}

	public function notContains($value, $failMessage = null)
	{
		$result = $this->notContainsValue($value, $failMessage, false);
        $this->getGenerator()->setPreviousAsserter('notContains');
        return $result;
	}
    
	public function hasKeys(array $keys, $failMessage = null)
	{
		if (sizeof($undefinedKeys = array_diff($keys, array_keys($this->value))) <= 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: sprintf($this->getLocale()->_('%s should have keys %s'), $this, $this->getTypeOf($undefinedKeys)));
		}

        $this->getGenerator()->setPreviousAsserter('hasKeys');
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
			$this->fail($failMessage ?: sprintf($this->getLocale()->_('%s should not have keys %s'), $this, $this->getTypeOf($definedKeys)));
		}

        $this->getGenerator()->setPreviousAsserter('notHasKeys');
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
			$this->fail($failMessage ?: sprintf($this->getLocale()->_('%s has no key %s'), $this, $this->getTypeOf($key)));
		}

        $this->getGenerator()->setPreviousAsserter('hasKey');
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
			$this->fail($failMessage ?: sprintf($this->getLocale()->_('%s has a key %s'), $this, $this->getTypeOf($key)));
		}

        $this->getGenerator()->setPreviousAsserter('notHasKey');
		return $this;
	}

	public function containsValues(array $values, $failMessage = null)
	{
		$result = $this->intersect($values, $failMessage, false);
        $this->getGenerator()->setPreviousAsserter('containsValues');
        return $result;
	}

	public function strictlyContainsValues(array $values, $failMessage = null)
	{
		$result = $this->intersect($values, $failMessage, true);
        $this->getGenerator()->setPreviousAsserter('strictlyContainsValues');
        return $result;
	}

	public function notContainsValues(array $values, $failMessage = null)
	{
		$result = $this->notIntersect($values, $failMessage, false);
        $this->getGenerator()->setPreviousAsserter('notContainsValues');
        return $result;
	}

	public function strictlyNotContainsValues(array $values, $failMessage = null)
	{
		$result = $this->notIntersect($values, $failMessage, true);
        $this->getGenerator()->setPreviousAsserter('notContainsValues');
        return $result;
	}

	protected function containsValue($value, $failMessage, $strict)
	{
        if($this->getGenerator()->getPreviousAsserter() !== 'atKey') 
        {
            $key = array_search($value, $this->valueIsSet()->value, $strict);
            if ($key !== false)
            {
                $this->lastContainedValueKey = $key;
                $this->lastContainedValueChecked = $value;
                $this->pass();
            }
            elseif ($strict === false)
            {
                $this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not contain %s'), $this, $this->getTypeOf($value)));
            }
            else
            {
                $this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not strictly contain %s'), $this, $this->getTypeOf($value)));
            }
        } 
        else 
        {
            if(($strict && $value === $this->lastContainedValueChecked) || (!$strict && $value == $this->lastContainedValueChecked))
            {
                $this->lastContainedValueKey = null;
                $this->lastContainedValueChecked = null;
                $this->pass();
            }
            else
            {
                $message = $strict ? '%s[%s] does not strictly contain %s' : '%s[%s] does not contain %s';
                $this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_($message), $this, $this->getTypeOf($this->lastContainedValueKey), $this->getTypeOf($value)));
            }
        }
        
        $this->getGenerator()->setPreviousAsserter('containsValues');
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

        $this->getGenerator()->setPreviousAsserter('notContainsValues');
		return $this;
	}

   public function atKey($key, $failMessage = null)
   {
       
       $array = $this->valueIsSet()->value;

       if($this->getGenerator()->getPreviousAsserter() && in_array($this->getGenerator()->getPreviousAsserter(), array('contains', 'strictlyContains')))
       {
            if($this->lastContainedValueKey === $key)
            {
                $this->lastContainedValueKey = null;
                $this->lastContainedValueChecked = null;
                $this->pass();
            } 
            else 
            {
                if(isset($array[$key])) 
                {
                    $this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s contains %s at key %s, not %s'), $this, $this->getTypeOf($this->lastContainedValueChecked), $this->getTypeOf($this->lastContainedValueKey), $this->getTypeOf($key)));
                }
                else 
                {
                    $this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s contains %s at key %s, but %s has no key %s'), $this, $this->getTypeOf($this->lastContainedValueChecked), $this->getTypeOf($this->lastContainedValueKey), $this, $this->getTypeOf($key)));
                }
            }    
       } 
       else 
       {
           if(isset($array[$key]))
           {
               $this->lastContainedValueKey = $key;
               $this->lastContainedValueChecked = $array[$key];
               $this->getGenerator()->setExpectedAsserter(array('contains', 'strictlyContains', 'notContains', 'strictlyNotContains'));
               $this->pass();
           } 
           else 
           {
               $this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has no key %s'), $this, $this->getTypeOf($key))));
           }
       }
       
       $this->getGenerator()->setPreviousAsserter('atKey');
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
    
    public function valueWasChecked()
    {
        $this->valueIsSet();
        return (bool) $this->lastContainedValueChecked;
    }
    
}
