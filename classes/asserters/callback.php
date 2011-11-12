<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class callback extends asserters\variable
{
	public function setWith($value, $label = null)
	{
		parent::setWith($value, $label);

		if (self::isCallback($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a callback'), $this));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function isCallable($failMessage = null)
	{
		if (is_callable($this->valueIsSet()->value) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not a callable'), $this));
		}

		return $this;
	}

	public function isClosure($failMessage = null)
	{
		if ($this->valueIsSet()->value instanceof \Closure)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not a instance of %s'), $this, 'Closure'));
		}

		return $this;
	}

	public function isFunction($failMessage = null)
	{
		if (is_string($this->valueIsSet()->value) && strpos($this->valueIsSet()->value, '::') === false)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not a function'), $this));
		}

		return $this;
	}

	public function isMethod($failMessage = null)
	{
		if ((is_string($this->valueIsSet()->value) && strpos($this->valueIsSet()->value, '::') !== false) || is_array($this->valueIsSet()->value))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not a method'), $this));
		}

		return $this;
	}

	public function isStaticMethod($failMessage = null)
	{
		if ((is_string($this->valueIsSet()->value) && strpos($this->valueIsSet()->value, '::') !== false) || (is_array($this->valueIsSet()->value) && is_string($this->valueIsSet()->value[0])))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not a static method'), $this));
		}

		return $this;
	}

	public function isInstanceMethod($failMessage = null)
	{
		if (is_array($this->valueIsSet()->value) && is_object($this->valueIsSet()->value[0]))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not a instance method'), $this));
		}

		return $this;
	}

	protected static function check($value, $method)
	{
		if (self::isCallback($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be a valid format callback');
		}
	}

	protected static function isCallback($value)
	{
		return (is_callable($value, true) === true);
	}
}

?>
