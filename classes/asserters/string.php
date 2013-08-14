<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class string extends asserters\variable
{
	protected $charlist = null;
	protected $streamController = null;
	protected $adapter = null;

	public function __construct(asserter\generator $generator = null, atoum\adapter $adapter = null)
	{
		parent::__construct($generator);

		$this->setAdapter($adapter);
	}

	public function __get($asserter)
	{
		switch ($asserter)
		{
			case 'length':
				return $this->getLengthAsserter();

			default:
				return $this->generator->__get($asserter);
		}
	}

	public function __toString()
	{
		return (is_string($this->value) === false ? parent::__toString() : sprintf($this->getLocale()->_('string(%s) \'%s\''), strlen($this->value), addcslashes($this->value, $this->charlist)));
	}

	public function setAdapter(atoum\adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new atoum\adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function getCharlist()
	{
		return $this->charlist;
	}

	public function setWith($value, $label = null, $charlist = null, $checkType = true)
	{
		parent::setWith($value, $label);

		$this->charlist = $charlist;

		if ($checkType === true)
		{
			if (self::isString($this->value) === true)
			{
				$this->pass();
			}
			else
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not a string'), $this));
			}
		}

		return $this;
	}

	public function isEmpty($failMessage = null)
	{
		return $this->isEqualTo('', $failMessage);
	}

	public function isNotEmpty($failMessage = null)
	{
		return $this->isNotEqualTo('', $failMessage !== null ? $failMessage : $this->getLocale()->_('string is empty'));
	}

	public function match($pattern, $failMessage = null)
	{
		if (preg_match($pattern, $this->valueIsSet()->value) === 1)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not match %s'), $this, $pattern));
		}

		return $this;
	}

	public function isEqualTo($value, $failMessage = null)
	{
		return parent::isEqualTo($value, $failMessage !== null ? $failMessage : $this->getLocale()->_('strings are not equals'));
	}

	public function isEqualToContentsOfFile($path, $failMessage = null)
	{
		$fileContents = @$this->valueIsSet()->adapter->file_get_contents($path);

		if ($fileContents === false)
		{
			$this->fail(sprintf($this->getLocale()->_('Unable to get contents of file %s'), $path));
		}
		else
		{
			return parent::isEqualTo($fileContents, $failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('string is not equals to contents of file %s'), $path));
		}
	}

	public function hasLength($length, $failMessage = null)
	{
		if (strlen($this->valueIsSet()->value) == $length)
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
		if (strlen($this->valueIsSet()->value) > $length)
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
		if (strlen($this->valueIsSet()->value) < $length)
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
		if (strpos($this->valueIsSet()->value, $fragment) !== false)
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
		if (strpos($this->valueIsSet()->value, $fragment) !== false)
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
		if (strpos($this->valueIsSet()->value, $fragment) === 0)
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
		if (strpos($this->valueIsSet()->value, $fragment) === 0)
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
		if (strpos($this->valueIsSet()->value, $fragment) === (strlen($this->valueIsSet()->value) - strlen($fragment)))
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
		if (strpos($this->valueIsSet()->value, $fragment) === (strlen($this->valueIsSet()->value) - strlen($fragment)))
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('String end with %s'), $fragment));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	protected function getLengthAsserter()
	{
		return $this->generator->__call('integer', array(strlen($this->valueIsSet()->value)));
	}

	protected static function check($value, $method)
	{
		if (self::isString($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be a string');
		}
	}

	protected static function isString($value)
	{
		return (is_string($value) === true);
	}
}
