<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class arrayAccess extends asserters\object
{
	public function setWith($value, $checkType = true)
	{
		parent::setWith($value, $checkType);

		if ($checkType === true)
		{
			if (self::isArrayAccess($this->value) === false)
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

    public function hasKey($key, $failMessage = null)
    {
        if ($this->valueIsSet()->value->offsetExists($key))
        {
            $this->pass();
        }
        else
        {
            $this->fail($failMessage ?: sprintf($this->getLocale()->_('%s has no key %s'), $this, $this->getTypeOf($key)));
        }

        return $this;
    }

	protected static function isArrayAccess($value)
	{
		return ($value instanceof \arrayAccess);
	}
}
