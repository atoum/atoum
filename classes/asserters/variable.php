<?php

namespace mageekguy\atoum\asserters;

class variable extends \mageekguy\atoum\asserter
{
	protected $isSet = false;
	protected $variable = null;

	public function __toString()
	{
		return $this->toString($this->variable);
	}

	public function setWith($variable)
	{
		$this->variable = $variable;

		if ($this->isSet === false)
		{
			$this->isSet = true;
		}

		return $this;
	}

	public function getVariable()
	{
		return $this->variable;
	}

	public function isEqualTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		$this->variableIsSet()->variable == $variable ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not equal to %s'), $this, $this->toString($variable)));

		return $this;
	}

	public function isNotEqualTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		$this->variableIsSet()->variable != $variable ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is equal to %s'), $this, $this->toString($variable)));

		return $this;
	}

	public function isIdenticalTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		$this->variableIsSet()->variable === $variable ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not identical to %s'), $this, $this->toString($variable)));

		return $this;
	}

	public function isNotIdenticalTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		$this->variableIsSet()->variable !== $variable ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is identical to %s'), $this, $this->toString($variable)));

		return $this;
	}

	public function isNull($failMessage = null)
	{
		return $this->variableIsSet()->isIdenticalTo(null, $failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not null'), $this));
	}

	public function isNotNull($failMessage = null)
	{
		return $this->isNotIdenticalTo(null, $failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is null'), $this));
	}

	protected function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new \logicException('Argument must be set at index 0');
		}

		return $this->setWith($arguments[0]);
	}

	protected function variableIsSet($message = 'Variable is undefined')
	{
		if ($this->isSet === false)
		{
			throw new \logicException($message);
		}

		return $this;
	}

	protected static function check($variable, $method) {}
}

?>
