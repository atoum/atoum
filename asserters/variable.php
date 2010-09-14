<?php

namespace mageekguy\atoum\asserters;

class variable extends \mageekguy\atoum\asserter
{
	protected $variable = null;

	public function __toString()
	{
		return $this->toString($this->variable);
	}

	public function setWith($variable)
	{
		$this->variable = $variable;

		return $this;
	}

	public function getVariable()
	{
		return $this->variable;
	}

	public function isEqualTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		$this->variable == $variable ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not equal to %s'), $this, $this->toString($variable)));

		return $this;
	}

	public function isNotEqualTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		$this->variable != $variable ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is equal to %s'), $this, $this->toString($variable)));

		return $this;
	}

	public function isIdenticalTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		$this->variable === $variable ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not identical to %s'), $this, $this->toString($variable)));

		return $this;
	}

	public function isNull($failMessage = null)
	{
		return $this->isIdenticalTo(null, $failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not null'), $this));
	}

	protected function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new \logicException('Argument must be set at index 0');
		}

		return $this->setWith($arguments[0]);
	}

	protected static function check($variable, $method) {}
}

?>
