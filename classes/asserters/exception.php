<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\exceptions
;

class exception extends object
{
	protected static $lastValue = null;

	public function __get($asserter)
	{
		switch (strtolower($asserter))
		{
			case 'hasdefaultcode':
			case 'hasnestedexception':
				return $this->{$asserter}();

			case 'message':
				return $this->getMessageAsserter();

			default:
				return $this->generator->__get($asserter);
		}
	}

	public function setWith($value, $checkType = true)
	{
		$exception = $value;

		if ($exception instanceof \closure)
		{
			$exception = null;

			if (version_compare(PHP_VERSION, '7.0.0') >= 0)
			{
				try
				{
					$value($this->getTest());
				}
				catch (\throwable $exception) {}
			}
			else
			{
				try
				{
					$value($this->getTest());
				}
				catch (\exception $exception) {}
			}

		}

		parent::setWith($exception, false);

		if ($checkType === true)
		{
			if (self::isThrowable($exception) === false)
			{
				$this->fail($this->_('%s is not an exception', $this));
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
			$this->check($value, __FUNCTION__);
		}
		catch (\logicException $exception)
		{
			if (self::classExists($value) === false || (strtolower(ltrim($value, '\\')) !== 'exception' && is_subclass_of($value, version_compare(PHP_VERSION, '7.0.0') >= 0 ? 'throwable' : 'exception') === false))
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
			$this->fail($failMessage ?: $this->_('code is %s instead of 0', $this->value->getCode()));
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
			$this->fail($failMessage ?: $this->_('code is %s instead of %s', $this->value->getCode(), $code));
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
			$this->fail($failMessage ?: $this->_('message \'%s\' is not identical to \'%s\'', $this->value->getMessage(), $message));
		}

		return $this;
	}

	public function hasNestedException(\exception $exception = null, $failMessage = null)
	{
		$nestedException = $this->valueIsSet()->value->getPrevious();

		if (($exception === null && $nestedException !== null) || ($exception !== null && $nestedException == $exception))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: ($exception === null ? $this->_('exception does not contain any nested exception') : $this->_('exception does not contain this nested exception')));
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
		return $this->generator->__call('phpString', array($this->valueIsSet()->value->getMessage()));
	}

	protected function check($value, $method)
	{
		if (self::isThrowable($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . __CLASS__ . '::' . $method . '() must be an exception instance');
		}

		return $this;
	}

	private static function isThrowable($value)
	{
		return $value instanceof \exception || (version_compare(PHP_VERSION, '7.0.0') >= 0 && $value instanceof \throwable);
	}
}
