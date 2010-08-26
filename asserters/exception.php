<?php

namespace mageekguy\atoum\asserters;

class exception extends \mageekguy\atoum\asserters\object
{
	public function isInstanceOf($variable)
	{
		try
		{
			self::check($variable, __METHOD__);
		}
		catch (\logicException $exception)
		{
			if (self::classExists($variable) === false || ($variable !== '\exception' && is_subclass_of($variable, '\exception') === false))
			{
				throw new \logicException('Argument of ' . __METHOD__ . '() must be an \exception instance or an exception class name');
			}
		}

		return parent::isInstanceOf($variable);
	}

	public function hasDefaultCode()
	{
		if (self::isException($this->variable) === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not an exception'), $this->variable));
		}

		$this->variable->getCode() === 0 ? $this->pass() : $this->fail(sprintf($this->locale->_('Code is %s instead of 0'), $this->variable->getCode()));

		return $this;
	}

	public function hasMessage($message)
	{
		if (self::isException($this->variable) === false)
		{
			$this->fail(sprintf($this->locale->_('Message not found because %s is not an exception'), $this->variable));
		}

		$this->variable->getMessage() == (string) $message ? $this->pass() : $this->fail(sprintf($this->locale->_('Message \'%s\' is not identical to \'%s\''), $this->variable->getMessage(), $message));

		return $this;
	}

	protected static function check($variable, $method)
	{
		if (self::isException($variable) === false)
		{
			throw new \logicException('Argument of ' . $method . '() must be an exception instance');
		}
	}

	protected static function isException($variable)
	{
		return (is_object($variable) === true && $variable instanceof \exception === true);
	}
}

?>
