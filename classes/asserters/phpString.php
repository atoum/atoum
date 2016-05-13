<?php

namespace mageekguy\atoum\asserters;

class phpString extends variable
{
	protected $charlist = null;

	public function __get($asserter)
	{
		switch (strtolower($asserter))
		{
			case 'length':
				return $this->getLengthAsserter();

			case 'isempty':
				return $this->isEmpty();

			case 'isnotempty':
				return $this->isNotEmpty();

			case 'toArray':
				return $this->toArray();

			default:
				return $this->generator->__get($asserter);
		}
	}

	public function __toString()
	{
		return (is_string($this->value) === false ? parent::__toString() : $this->_('string(%s) \'%s\'', strlen($this->value), addcslashes($this->value, $this->charlist)));
	}

	public function getCharlist()
	{
		return $this->charlist;
	}

	public function setWith($value, $charlist = null, $checkType = true)
	{
		parent::setWith($value);

		$this->charlist = $charlist;

		if ($checkType === true)
		{
			if ($this->analyzer->isString($this->value) === true)
			{
				$this->pass();
			}
			else
			{
				$this->fail($this->_('%s is not a string', $this));
			}
		}

		return $this;
	}

	public function isEmpty($failMessage = null)
	{
		return $this->isEqualTo('', $failMessage ?: $this->_('string is not empty'));
	}

	public function isNotEmpty($failMessage = null)
	{
		return $this->isNotEqualTo('', $failMessage ?: $this->_('string is empty'));
	}

	public function match($pattern, $failMessage = null)
	{
		return $this->matches($pattern, $failMessage);
	}

	public function matches($pattern, $failMessage = null)
	{
		if (preg_match($pattern, $this->valueIsSet()->value) === 1)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s does not match %s', $this, $pattern));
		}

		return $this;
	}

	public function isEqualTo($value, $failMessage = null)
	{
		return parent::isEqualTo($value, $failMessage ?: $this->_('strings are not equal'));
	}

	public function isEqualToContentsOfFile($path, $failMessage = null)
	{
		$this->valueIsSet();

		$fileContents = @file_get_contents($path);

		if ($fileContents === false)
		{
			$this->fail($this->_('Unable to get contents of file %s', $path));
		}
		else
		{
			return parent::isEqualTo($fileContents, $failMessage ?: $this->_('string is not equal to contents of file %s', $path));
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
			$this->fail($failMessage ?: $this->_('length of %s is not %d', $this, $length));
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
			$this->fail($failMessage ?: $this->_('length of %s is not greater than %d', $this, $length));
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
			$this->fail($failMessage ?: $this->_('length of %s is not less than %d', $this, $length));
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
			$this->fail($failMessage ?: $this->_('%s does not contain %s', $this, $fragment));
		}

		return $this;
	}

	public function notContains($fragment, $failMessage = null)
	{
		if (strpos($this->valueIsSet()->value, $fragment) !== false)
		{
			$this->fail($failMessage ?: $this->_('%s contains %s', $this, $fragment));
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
			$this->fail($failMessage ?: $this->_('%s does not start with %s', $this, $fragment));
		}

		return $this;
	}

	public function notStartWith($fragment, $failMessage = null)
	{
		$fragmentPosition = strpos($this->valueIsSet()->value, $fragment);

		if ($fragmentPosition === false || $fragmentPosition > 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s start with %s', $this, $fragment));
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
			$this->fail($failMessage ?: $this->_('%s does not end with %s', $this, $fragment));
		}

		return $this;
	}

	public function notEndWith($fragment, $failMessage = null)
	{
		if (strpos($this->valueIsSet()->value, $fragment) === (strlen($this->valueIsSet()->value) - strlen($fragment)))
		{
			$this->fail($failMessage ?: $this->_('%s end with %s', $this, $fragment));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function toArray()
	{
		return $this->generator->castToArray($this->valueIsSet()->value);
	}

	protected function getLengthAsserter()
	{
		return $this->generator->__call('integer', array(strlen($this->valueIsSet()->value)));
	}
}
