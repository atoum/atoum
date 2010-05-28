<?php

namespace mageekguy\tests\unit\asserters;

class boolean extends \mageekguy\sparkline\tests\asserter
{
	protected $mixed = null;

	public function __toString()
	{
		return self::toString($this->mixed);
	}

	public function setWith($mixed)
	{
		$this->mixed = $mixed;
		return $this->reset();
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
		if ($this->skip() === false)
		{
			$this->mixed === $mixed ? $this->pass() : $this->fail($this . ' is not equal to ' . self::toString($mixed));
		}

		return $this;
	}

	protected function setWithArguments(array $arguments)
	{
	}
}

?>
