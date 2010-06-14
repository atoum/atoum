<?php

namespace mageekguy\tests\unit\asserters;

class exception extends \mageekguy\tests\unit\asserters\object
{
	public function isInstanceOf($mixed)
	{
		try
		{
			self::check($mixed, __METHOD__);
		}
		catch (\logicException $exception)
		{
			if ((class_exists($mixed) === false && interface_exists($mixed) === false) || ($mixed !== '\exception' && is_subclass_of($mixed, '\exception') === false))
			{
				throw new \logicException('Argument of ' . __METHOD__ . '() must be an \exception instance or an exception class name');
			}
		}

		return parent::isInstanceOf($mixed);
	}

	public function hasDefaultCode()
	{
		if (self::isException($this->mixed) === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not an exception'), $this->mixed));
		}

		$this->mixed->getCode() === 0 ? $this->pass() : $this->fail(sprintf($this->locale->_('Code is %s instead of 0'), $this->mixed->getCode()));

		return $this;
	}

	public function hasMessage($message)
	{
		if (self::isException($this->mixed) === false)
		{
			$this->fail(sprintf($this->locale->_('Message not found because %s is not an exception'), $this->mixed));
		}

		$this->mixed->getMessage() == (string) $message ? $this->pass() : $this->fail(sprintf($this->locale->_('Message \'%s\' is not identical to \'%s\''), $this->mixed->getMessage(), $message));

		return $this;
	}

	protected static function check($mixed, $method)
	{
		if (self::isException($mixed) === false)
		{
			throw new \logicException('Argument of ' . $method . '() must be an exception instance');
		}
	}

	protected static function isException($mixed)
	{
		return (is_object($mixed) === true && $mixed instanceof \exception === true);
	}
}

?>
