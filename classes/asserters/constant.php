<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
;

class constant extends atoum\asserter
{
	protected $isSet = false;
	protected $value = null;

	public function __toString()
	{
		return $this->getTypeOf($this->value);
	}

	public function __call($method, $arguments)
	{
		switch (strtolower($method))
		{
			case 'equalto':
				return call_user_func_array(array($this, 'isEqualTo'), $arguments);

			default:
				return parent::__call($method, $arguments);
		}
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

		return $this;
	}

	public function reset()
	{
		$this->value = null;
		$this->isSet = false;

		return $this;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function isEqualTo($value, $failMessage = null)
	{
		if ($this->valueIsSet()->value === $value)
		{
			$this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(($failMessage ?: sprintf($this->getLocale()->_('%s is not equal to %s'), $this, $this->getTypeOf($value))) .  PHP_EOL .  $diff->setExpected($value)->setActual($this->value));
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
}
