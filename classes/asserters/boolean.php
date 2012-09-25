<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class boolean extends asserters\variable
{
	public function __get($property)
	{
		switch (true)
		{
			case strtolower($property) == 'isfalse':
				return $this->isFalse();

			case strtolower($property) == 'istrue':
				return $this->isTrue();

			default:
				return parent::__get($property);
		}
	}

	public function setWith($value, $label = null)
	{
		parent::setWith($value, $label);

		if (self::isBoolean($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a boolean'), $this));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function isTrue($failMessage = null)
	{
		return $this->isEqualTo(true, $failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not true'), $this));
	}

	public function isFalse($failMessage = null)
	{
		return $this->isEqualTo(false, $failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not false'), $this));
	}

	protected static function check($value, $method)
	{
		if (self::isBoolean($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be a boolean');
		}
	}

	protected static function isBoolean($value)
	{
		return (is_bool($value) === true);
	}
}
