<?php

namespace mageekguy\atoum\asserters;

class castToString extends phpString
{
	public function setWith($value, $charlist = null, $checkType = true)
	{
		parent::setWith($value, $charlist, false);

		if ($checkType === true)
		{
			if (self::isObject($value) === false)
			{
				$this->fail($this->_('%s is not an object', $this->getTypeOf($value)));
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
