<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class utf8String extends asserters\string
{
	public function __toString()
	{
		return (is_string($this->value) === false ? parent::__toString() : sprintf($this->getLocale()->_('string(%s) \'%s\''), mb_strlen($this->value, 'UTF-8'), addcslashes($this->value, $this->charlist)));
	}

	public function setWith($value, $label = null, $charlist = null, $checkType = true)
	{
		parent::setWith($value, $label, $charlist, $checkType);

		if ($checkType === true)
		{
			if (self::isUtf8String($this->value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not a unicode string'), $this));
			}
			else
			{
				$this->pass();
			}
		}

		return $this;
	}

	public function hasLength($length, $failMessage = null)
	{
		if (mb_strlen($this->valueIsSet()->value, 'UTF-8') == $length)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('length of %s is not %d'), $this, $length));
		}

		return $this;
	}

	public function contains($fragment, $failMessage = null)
	{
		if (mb_strpos($this->valueIsSet()->value, $fragment, 0, 'UTF-8') !== false)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('String does not contain %s'), $fragment));
		}

		return $this;
	}

	protected static function check($value, $method)
	{
		if (self::isString($value) === false || self::isUtf8String($this->value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be a string');
		}
	}

	protected static function isUtf8String($value)
	{
		return (empty($value) || mb_detect_encoding($value) === 'UTF-8');
	}
}