<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\test,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
;

class exception extends asserters\object
{
	protected static $lastValue = null;

	public function __get($asserter)
	{
		switch ($asserter)
		{
			case 'message':
				return $this->getMessageAsserter();

			default:
				return $this->generator->__get($asserter);
		}
	}

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

				static::$lastValue = $exception;
			}
		}

		return $this;
	}

	public function isInstanceOf($value, $failMessage = null)
	{
		try
		{
			self::check($value, __FUNCTION__);
		}
		catch (\logicException $exception)
		{
			if (self::classExists($value) === false || (strtolower(ltrim($value, '\\')) !== 'exception' && is_subclass_of($value, 'exception') === false))
			{
				throw new exceptions\logic\invalidArgument('Argument of ' . __METHOD__ . '() must be a \exception instance or an exception class name');
			}
		}

		return parent::isInstanceOf($value, $failMessage);
	}

	public function hasDefaultCode($failMessage = null)
	{
		if ($this->valueIsSet()->value->getCode() === 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('code is %s instead of 0'), $this->value->getCode()));
		}

		return $this;
	}

	public function hasCode($code, $failMessage = null)
	{
		if ($this->valueIsSet()->value->getCode() === $code)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('code is %s instead of %s'), $this->value->getCode(), $code));
		}

		return $this;
	}

	public function hasMessage($message, $failMessage = null)
	{
		if ($this->valueIsSet()->value->getMessage() == (string) $message)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('message \'%s\' is not identical to \'%s\''), $this->value->getMessage(), $message));
		}

		return $this;
	}

	public function hasNestedException(\exception $exception = null, $failMessage = null)
	{
		if ($exception === null)
		{
			if ($this->valueIsSet()->value->getPrevious() !== null)
			{
				$this->pass();
			}
			else
			{
				$this->fail($failMessage !== null ? $failMessage : $this->getLocale()->_('exception does not contain any nested exception'));
			}
		}
		else if ($this->valueIsSet()->value->getPrevious() == $exception)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : $this->getLocale()->_('exception does not contain this nested exception'));
		}

		return $this;
	}

	public static function getLastValue()
	{
		return static::$lastValue;
	}

	protected function valueIsSet($message = 'Exception is undefined')
	{
		return parent::valueIsSet($message);
	}

	protected function getMessageAsserter()
	{
		return $this->generator->__call('string', array($this->valueIsSet()->value->getMessage()));
	}

	protected static function check($value, $method)
	{
		if (self::isException($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . __CLASS__ . '::' . $method . '() must be an exception instance');
		}
	}

	protected static function isException($value)
	{
		return (parent::isObject($value) === true && $value instanceof \exception === true);
	}
}
