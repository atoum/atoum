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
		if (is_object($mixed) === false && class_exists($mixed, true) === false)
		{
			throw new \LogicException('Argument of ' . __METHOD__ . '() must be a valid class name');
		}
		else if ($mixed != '\exception' && is_subclass_of($mixed, '\exception') === false)
		{
			throw new \LogicException('Argument of ' . __METHOD__ . '() must be an exception');
		}
		else
		{
			$this->mixed instanceof $mixed ? $this->pass() : $this->fail(sprintf($this->locale->_('%s is not an instance of %s'), $this, (is_string($mixed) === true ? $mixed : get_class($mixed))));
			return $this;
		}
	}

	public function hasMessage($message)
	{
		$this->isInstanceOf('\exception');
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
}

?>
