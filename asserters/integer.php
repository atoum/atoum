<?php

namespace mageekguy\atoum\asserters;

class integer extends \mageekguy\atoum\asserter
{
	protected $integer = null;

	public function __construct(\mageekguy\atoum\score $score, \mageekguy\atoum\locale $locale)
	{
		parent::__construct($score, $locale);
	}

	public function __toString()
	{
		return self::toString($this->integer);
	}

	public function getInteger()
	{
		return $this->integer;
	}

	public function setWith($integer)
	{
		$this->integer = $integer;

		return $this;
	}

	public function isZero()
	{
		return $this->isEqualTo(0);
	}

	public function isEqualTo($integer)
	{
		if (is_integer($integer) === false)
		{
			throw new \logicException('Argument of ' . __METHOD__ . '() must be an integer');
		}

		$this->integer === $integer ? $this->pass() : $this->fail($this . ' is not equal to ' . self::toString($integer));

		return $this;
	}

	public function isNotEqualTo($integer)
	{
		if (is_integer($integer) === false)
		{
			throw new \logicException('Argument of ' . __METHOD__ . '() must be an integer');
		}

		$this->integer != $integer ? $this->pass() : $this->fail($this . ' is equal to ' . self::toString($integer));

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
