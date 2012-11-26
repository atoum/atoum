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
	public function __construct(asserter\generator $generator, atoum\adapter $adapter = null)
	{
		parent::__construct($generator, $adapter);

		if ($this->adapter->extension_loaded('mbstring') === false)
		{
			throw new exceptions\runtime('mbstring PHP extension is mandatory to use utf8String asserter');
		}
	}

	public function __toString()
	{
		return (is_string($this->value) === false ? parent::__toString() : sprintf($this->getLocale()->_('string(%s) \'%s\''), mb_strlen($this->value, 'UTF-8'), addcslashes($this->value, $this->charlist)));
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

	public function hasLengthGreaterThan($length, $failMessage = null)
	{
		if (mb_strlen($this->valueIsSet()->value, 'UTF-8') > $length)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('length of %s is not greater than %d'), $this, $length));
		}

		return $this;
	}

	public function hasLengthLessThan($length, $failMessage = null)
	{
		if (mb_strlen($this->valueIsSet()->value, 'UTF-8') < $length)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('length of %s is not less than %d'), $this, $length));
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

	public function getTypeOf($mixed)
	{
		if(is_string($mixed))
		{
			return sprintf($this->getLocale()->_('string(%s) \'%s\''), mb_strlen($mixed, 'UTF-8'), $mixed);
		}
		else
		{
			return parent::getTypeOf($mixed);
		}
	}
}