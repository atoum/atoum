<?php

namespace mageekguy\atoum\asserters;

class integer extends \mageekguy\atoum\asserter
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

	public function isEqualTo($mixed)
	{
		if (is_integer($mixed) === false)
		{
			throw new \logicException('Argument of ' . __METHOD__ . '() must be an integer');
		}

		$this->mixed === $mixed ? $this->pass() : $this->fail($this . ' is not equal to ' . self::toString($mixed));
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
