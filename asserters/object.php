<?php

namespace mageekguy\tests\unit\asserters;

class object extends \mageekguy\tests\unit\asserter
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
		if (is_object($mixed) === false && class_exists($mixed) === false && interface_exists($mixed) === false)
		{
			throw new \logicException('Argument of ' . __METHOD__ . '() must be a class instance or a class name');
		}

		$this->mixed instanceof $mixed ? $this->pass() : $this->fail(sprintf($this->locale->_('%s is not an instance of %s'), $this, is_string($mixed) === true ? $mixed : self::toString($mixed)));

		return $this;
	}

	public function isIdenticalTo($mixed)
	{
		if (is_object($mixed) === false)
		{
			throw new \logicException('Argument of ' . __METHOD__ . '() must be a class instance');
		}

		$this->mixed === $mixed ? $this->pass() : $this->fail(sprintf($this->locale->_('%s is not identical to %s'), $this, self::toString($this)));

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
