<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters
;

class castToString extends asserters\string
{
	public function setWith($value, $label = null, $charlist = null, $checkType = true)
	{
		parent::setWith($value, $label, $charlist, false);

		if ($checkType === true)
		{
			if (self::isObject($value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not an object'), $this->getTypeOf($value)));
			}
			else
			{
				$this->pass();

				$this->value = (string) $this->value;
			}
		}

		return $this;
	}

	protected static function isObject($value)
	{
		return (is_object($value) === true);
	}
}
