<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\tools,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

class utf8String extends phpString
{
	protected $adapter = null;

	public function __construct(asserter\generator $generator = null, tools\variable\analyzer $analyzer = null, atoum\locale $locale = null)
	{
		if (extension_loaded('mbstring') === false)
		{
			throw new exceptions\runtime('mbstring PHP extension is mandatory to use utf8String asserter');
		}

		parent::__construct($generator, $analyzer, $locale);
	}

	public function __toString()
	{
		return (is_string($this->value) === false ? parent::__toString() : $this->_('string(%s) \'%s\'', mb_strlen($this->value, 'UTF-8'), addcslashes($this->value, $this->charlist)));
	}

	public function setWith($value, $charlist = null, $checkType = true)
	{
		parent::setWith($value, $charlist, $checkType);

		if ($checkType === true)
		{
			if ($this->analyzer->isUtf8($this->value) === true)
			{
				$this->pass();
			}
			else
			{
				$this->fail($this->_('%s is not an UTF-8 string', $this));
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
			$this->fail($failMessage ?: $this->_('length of %s is not %d', $this, $length));
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
			$this->fail($failMessage ?: $this->_('length of %s is not greater than %d', $this, $length));
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
			$this->fail($failMessage ?: $this->_('length of %s is not less than %d', $this, $length));
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
			$this->fail($failMessage ?: $this->_('%s does not contain %s', $this, $fragment));
		}

		return $this;
	}

	public function notContains($fragment, $failMessage = null)
	{
		if (mb_strpos($this->valueIsSet()->value, $fragment, 0, 'UTF-8') !== false)
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
		if (mb_strpos($this->valueIsSet()->value, $fragment, 0, 'UTF-8') === 0)
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
		if (mb_strpos($this->valueIsSet()->value, $fragment, 0, 'UTF-8') === 0)
		{
			$this->fail($failMessage ?: $this->_('%s start with %s', $this, $fragment));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function endWith($fragment, $failMessage = null)
	{
		if (mb_strpos($this->valueIsSet()->value, $fragment, 0, 'UTF-8') === (mb_strlen($this->valueIsSet()->value, 'UTF-8') - mb_strlen($fragment, 'UTF-8')))
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
		if (mb_strpos($this->valueIsSet()->value, $fragment, 0, 'UTF-8') === (mb_strlen($this->valueIsSet()->value, 'UTF-8') - mb_strlen($fragment, 'UTF-8')))
		{
			$this->fail($failMessage ?: $this->_('%s end with %s', $this, $fragment));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	protected function getLengthAsserter()
	{
		return $this->generator->__call('integer', array(mb_strlen($this->valueIsSet()->value, 'UTF-8')));
	}
}
