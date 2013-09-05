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
	protected $adapter = null;

	public function __construct(asserter\generator $generator = null, atoum\adapter $adapter = null)
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

	public function setWith($value, $label = null, $charlist = null, $checkType = true)
	{
		parent::setWith($value, $label, $charlist, $checkType);

		if ($checkType === true)
		{
			if (static::isUtf8($this->value) === true)
			{
				$this->pass();
			}
			else
			{
				$this->fail(sprintf($this->getLocale()->_('\'%s\' is not an UTF-8 string'), $value));
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
		if (static::isUtf8($fragment) === false)
		{
			throw new exceptions\logic\invalidArgument('Fragment \'' . $fragment . '\' is not an UTF-8 string');
		}

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

	public function notContains($fragment, $failMessage = null)
	{
		if (static::isUtf8($fragment) === false)
		{
			throw new exceptions\logic\invalidArgument('Fragment \'' . $fragment . '\' is not an UTF-8 string');
		}

		if (mb_strpos($this->valueIsSet()->value, $fragment, 0, 'UTF-8') !== false)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('String contains %s'), $fragment));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function startWith($fragment, $failMessage = null)
	{
		if (static::isUtf8($fragment) === false)
		{
			throw new exceptions\logic\invalidArgument('Fragment \'' . $fragment . '\' is not an UTF-8 string');
		}

		if (mb_strpos($this->valueIsSet()->value, $fragment, 0, 'UTF-8') === 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('String does not start with %s'), $fragment));
		}

		return $this;
	}

	public function notStartWith($fragment, $failMessage = null)
	{
		if (static::isUtf8($fragment) === false)
		{
			throw new exceptions\logic\invalidArgument('Fragment \'' . $fragment . '\' is not an UTF-8 string');
		}

		if (mb_strpos($this->valueIsSet()->value, $fragment, 0, 'UTF-8') === 0)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('String start with %s'), $fragment));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function endWith($fragment, $failMessage = null)
	{
		if (static::isUtf8($fragment) === false)
		{
			throw new exceptions\logic\invalidArgument('Fragment \'' . $fragment . '\' is not an UTF-8 string');
		}

		if (mb_strpos($this->valueIsSet()->value, $fragment, 0, 'UTF-8') === (mb_strlen($this->valueIsSet()->value) - mb_strlen($fragment)))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('String does not end with %s'), $fragment));
		}

		return $this;
	}

	public function notEndWith($fragment, $failMessage = null)
	{
		if (static::isUtf8($fragment) === false)
		{
			throw new exceptions\logic\invalidArgument('Fragment \'' . $fragment . '\' is not an UTF-8 string');
		}

		if (mb_strpos($this->valueIsSet()->value, $fragment, 0, 'UTF-8') === (mb_strlen($this->valueIsSet()->value) - mb_strlen($fragment)))
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('String end with %s'), $fragment));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function getTypeOf($mixed)
	{
		return (is_string($mixed) === false ? parent::getTypeOf($mixed) : sprintf($this->getLocale()->_('string(%s) \'%s\''), mb_strlen($mixed, 'UTF-8'), $mixed));
	}

	protected function getLengthAsserter()
	{
		return $this->generator->__call('integer', array(mb_strlen($this->valueIsSet()->value, 'UTF-8')));
	}

	protected static function isUtf8($string)
	{
		return (preg_match('/^.*$/us', $string) === 1);
	}
}
