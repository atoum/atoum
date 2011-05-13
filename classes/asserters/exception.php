<?php

namespace mageekguy\atoum\asserters;

use
	\mageekguy\atoum\asserters,
	\mageekguy\atoum\exceptions
;

class exception extends asserters\object
{
	public function setWith($value, $label = null, $check = true)
	{
		$exception = $value;

		if ($exception instanceof \closure)
		{
			$exception = null;

			try
			{
				$value();
			}
			catch (\exception $exception) {}
		}

		parent::setWith($exception, $label, false);

		if ($check === true)
		{
			if (self::isException($exception) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not an exception'), $this));
			}
			else
			{
				$this->pass();
			}
		}

		return $this;
	}

	public function isInstanceOf($value, $failMessage = null)
	{
		try
		{
			self::check($value, __METHOD__);
		}
		catch (\logicException $exception)
		{
			if (self::classExists($value) === false || ($value !== '\exception' && is_subclass_of($value, '\exception') === false))
			{
				throw new exceptions\logic\invalidArgument('Argument of ' . __METHOD__ . '() must be an \exception instance or an exception class name');
			}
		}

		return parent::isInstanceOf($value, $failMessage);
	}

	public function hasDefaultCode($failMessage = null)
	{
		if (self::isException($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not an exception'), $this->value));
		}

		if ($this->value->getCode() === 0)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('code is %s instead of 0'), $this->value->getCode()));
		}
	}

	public function hasCode($code, $failMessage = null)
	{
		if (self::isException($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('code not found because %s is not an exception'), $this->value));
		}

		if ($this->value->getCode() === $code)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('code is %s instead of %s'), $this->value->getCode(),$code));
		}
	}

	public function hasMessage($message, $failMessage = null)
	{
		if (self::isException($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('message not found because %s is not an exception'), $this->value));
		}

		if ($this->value->getMessage() == (string) $message)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('message \'%s\' is not identical to \'%s\''), $this->value->getMessage(), $message));
		}
	}

	protected static function check($value, $method)
	{
		if (self::isException($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be an exception instance');
		}
	}

	protected static function isException($value)
	{
		return (parent::isObject($value) === true && $value instanceof \exception === true);
	}
}

?>
