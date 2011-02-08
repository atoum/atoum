<?php

namespace mageekguy\atoum\asserters;

use \mageekguy\atoum\exceptions;

class exception extends \mageekguy\atoum\asserters\object
{
	public function setWith($variable, $check = true)
	{
		$exception = $variable;

		if ($exception instanceof \closure)
		{
			$exception = null;

			try
			{
				$variable();
			}
			catch (\exception $exception) {}
		}

		parent::setWith($exception, false);

		if ($check === true)
		{
			if (self::isException($exception) === false)
			{
				$this->fail(sprintf($this->locale->_('%s is not an exception'), $this));
			}
			else
			{
				$this->pass();
			}
		}

		return $this;
	}

	public function isInstanceOf($variable, $failMessage = null)
	{
		try
		{
			self::check($variable, __METHOD__);
		}
		catch (\logicException $exception)
		{
			if (self::classExists($variable) === false || ($variable !== '\exception' && is_subclass_of($variable, '\exception') === false))
			{
				throw new exceptions\logic\invalidArgument('Argument of ' . __METHOD__ . '() must be an \exception instance or an exception class name');
			}
		}

		return parent::isInstanceOf($variable, $failMessage);
	}

	public function hasDefaultCode($failMessage = null)
	{
		if (self::isException($this->variable) === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not an exception'), $this->variable));
		}

		if ($this->variable->getCode() === 0)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('code is %s instead of 0'), $this->variable->getCode()));
		}
	}
	
	public function hasCode($code, $failMessage = null)
	{
		if (self::isException($this->variable) === false)
		{
			$this->fail(sprintf($this->locale->_('code not found because %s is not an exception'), $this->variable));
		}

		if ($this->variable->getCode() === $code)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('code is %s instead of %s'), $this->variable->getCode(),$code));
		}
	}

	public function hasMessage($message, $failMessage = null)
	{
		if (self::isException($this->variable) === false)
		{
			$this->fail(sprintf($this->locale->_('message not found because %s is not an exception'), $this->variable));
		}

		if ($this->variable->getMessage() == (string) $message)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('message \'%s\' is not identical to \'%s\''), $this->variable->getMessage(), $message));
		}
	}

	protected static function check($variable, $method)
	{
		if (self::isException($variable) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be an exception instance');
		}
	}

	protected static function isException($variable)
	{
		return (parent::isObject($variable) === true && $variable instanceof \exception === true);
	}
}

?>
