<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

class string extends variable
{
	protected $charlist = null;

	public function __construct(asserter\generator $generator, atoum\adapter $adapter = null)
	{
		parent::__construct($generator);

		$this->adapter = $adapter ?: new atoum\adapter();
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function __toString()
	{
		return (is_string($this->value) === false ? parent::__toString() : sprintf($this->getLocale()->_('string(%s) \'%s\''), strlen($this->value), addcslashes($this->value, $this->charlist)));
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
			if (self::isString($this->value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not a string'), $this));
			}
			else
			{
				$this->pass();
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
