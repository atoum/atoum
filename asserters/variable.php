<?php

namespace mageekguy\tests\unit\asserters;

class variable extends \mageekguy\tests\unit\asserter
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
		self::check($mixed, __METHOD__);

		$this->mixed == $mixed ? $this->pass() : $this->fail(sprintf($this->locale->_('Value \'%s\' is not equal to value \'%s\''), $this->mixed, $mixed));

		return $this;
	}

	public function isNotEqualTo($mixed)
	{
		self::check($mixed, __METHOD__);

		$this->mixed != $mixed ? $this->pass() : $this->fail(sprintf($this->locale->_('Value \'%s\' is equal to value \'%s\''), $this->mixed, $mixed));

		return $this;
	}

	public function isIdenticalTo($mixed)
	{
		self::check($mixed, __METHOD__);

		$this->mixed === $mixed ? $this->pass() : $this->fail(sprintf($this->locale->_('Value \'%s\' is not identical to value \'%s\''), $this->mixed, $mixed));

		return $this;
	}

	public function isNull()
	{
		$this->mixed === null ? $this->pass() : $this->fail(sprintf($this->locale->_('Value \'%s\' is not null'), $this->mixed));

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

	protected static function check($mixed, $method) {}
}

?>
