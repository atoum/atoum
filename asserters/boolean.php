<?php

namespace mageekguy\tests\unit\asserters;

class boolean extends \mageekguy\tests\unit\asserter
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

	public function isTrue()
	{
		return $this->isEqualTo(true);
	}

	public function isFalse()
	{
		return $this->isEqualTo(false);
	}

	public function isEqualTo($mixed)
	{
		$this->mixed === $mixed ? $this->pass() : $this->fail(sprintf($this->locale->_('%s is not equal to %s'), $this, self::toString($mixed)));
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
