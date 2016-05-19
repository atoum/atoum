<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\tools,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
;

class variable extends asserter
{
	protected $diff = null;
	protected $isSet = false;
	protected $value = null;
	protected $isSetByReference = false;

	public function __construct(asserter\generator $generator = null, tools\variable\analyzer $analyzer = null, atoum\locale $locale = null)
	{
		parent::__construct($generator, $analyzer, $locale);

		$this->setDiff();
	}

	public function __toString()
	{
		return $this->getTypeOf($this->value);
	}

	public function __get($property)
	{
		switch (strtolower($property))
		{
			case 'isnull':
			case 'isnotnull':
			case 'isnotfalse':
			case 'isnottrue':
			case 'iscallable':
			case 'isnotcallable':
				return $this->{$property}();

			default:
				return parent::__get($property);
		}
	}

	public function __call($method, $arguments)
	{
		$assertion = null;

		switch ($method)
		{
			case '==':
				$assertion = 'isEqualTo';
				break;

			case '===':
				$assertion = 'isIdenticalTo';
				break;

			case '!=':
				$assertion = 'isNotEqualTo';
				break;

			case '!==':
				$assertion = 'isNotIdenticalTo';
				break;

			default:
				return parent::__call($method, $arguments);
		}

		return call_user_func_array(array($this, $assertion), $arguments);
	}

	public function setDiff(diffs\variable $diff = null)
	{
		$this->diff = $diff ?: new diffs\variable();

		return $this;
	}

	public function getDiff()
	{
		return $this->diff;
	}

	public function wasSet()
	{
		return ($this->isSet === true);
	}

	public function setWith($value)
	{
		parent::setWith($value);

		$this->value = $value;
		$this->isSet = true;
		$this->isSetByReference = false;

		return $this;
	}

	public function setByReferenceWith(& $value)
	{
		$this->reset();

		$this->value = & $value;
		$this->isSet = true;
		$this->isSetByReference = true;

		return $this;
	}

	public function reset()
	{
		$this->value = null;
		$this->isSet = false;
		$this->isSetByReference = false;

		return $this;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function isSetByReference()
	{
		return ($this->isSet === true && $this->isSetByReference === true);
	}

	public function isEqualTo($value, $failMessage = null)
	{
		if ($this->valueIsSet()->value == $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail(($failMessage ?: $this->_('%s is not equal to %s', $this, $this->getTypeOf($value))) . PHP_EOL . $this->diff($value));
		}

		return $this;
	}

	public function isNotEqualTo($value, $failMessage = null)
	{
		if ($this->valueIsSet()->value != $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is equal to %s', $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isIdenticalTo($value, $failMessage = null)
	{
		if ($this->valueIsSet()->value === $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not identical to %s', $this, $this->getTypeOf($value)) . PHP_EOL . $this->diff($value));
		}

		return $this;
	}

	public function isNotIdenticalTo($value, $failMessage = null)
	{
		if ($this->valueIsSet()->value !== $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is identical to %s', $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function isNull($failMessage = null)
	{
		if ($this->valueIsSet()->value === null)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not null', $this));
		}

		return $this;
	}

	public function isNotNull($failMessage = null)
	{
		if ($this->valueIsSet()->value !== null)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is null', $this));
		}

		return $this;
	}

	public function isReferenceTo(& $reference, $failMessage = null)
	{
		if ($this->valueIsSet()->isSetByReference() === false)
		{
			throw new exceptions\logic('Value is not set by reference');
		}

		if (is_object($this->value) === true && is_object($reference) === true)
		{
			if ($this->value === $reference)
			{
				$this->pass();
			}
			else
			{
				$this->fail($failMessage ?: $this->_('%s is not a reference to %s', $this, $this->getTypeOf($reference)));
			}
		}
		else
		{
			$tmp = $reference;
			$reference = uniqid(mt_rand());
			$isReference = ($this->value === $reference);
			$reference = $tmp;

			if ($isReference === true)
			{
				$this->pass();
			}
			else
			{
				$this->fail($failMessage ?: $this->_('%s is not a reference to %s', $this, $this->getTypeOf($reference)));
			}
		}

		return $this;
	}

	public function isNotFalse($failMessage = null)
	{
		return $this->isNotIdenticalTo(false, $failMessage ?: $this->_('%s is false', $this));
	}

	public function isNotTrue($failMessage = null)
	{
		return $this->isNotIdenticalTo(true, $failMessage ?: $this->_('%s is true', $this));
	}

	public function isCallable($failMessage = null)
	{
		if (is_callable($this->valueIsSet()->value) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not callable', $this));
		}

		return $this;
	}

	public function isNotCallable($failMessage = null)
	{
		if (is_callable($this->valueIsSet()->value) === false)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is callable', $this));
		}

		return $this;
	}

	protected function valueIsSet($message = 'Value is undefined')
	{
		if ($this->isSet === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}

	protected function diff($expected)
	{
		return $this->diff->setExpected($expected)->setActual($this->value);
	}
}
