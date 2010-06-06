<?php

namespace mageekguy\tests\unit\asserters;

class exception extends \mageekguy\tests\unit\asserter
{
	protected $mixed = null;

	public function __toString()
	{
		return self::toString($this->mixed);
	}

	public function setWith($mixed)
	{
		$this->mixed = $mixed;
		return $this;
	}

	public function isInstanceOf($mixed)
	{
		if (self::isException($mixed) === false)
		{
			throw new \LogicException('Argument of ' . __METHOD__ . '() must be an \exception class or on instance of class \exception');
		}

		$this->mixed instanceof $mixed ? $this->pass() : $this->fail(sprintf($this->locale->_('%s is not an instance of %s'), $this, (is_string($mixed) === true ? $mixed : get_class($mixed))));

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

	protected function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new \logicException('Argument must be set at index 0');
		}

		return $this->setWith($arguments[0]);
	}

	protected static function isException($mixed)
	{
		$mixed = (is_object($mixed) === false ? (string) $mixed : get_class($mixed));

		return ($mixed === '\exception' || is_subclass_of($mixed, '\exception') === true);
	}
}

?>
