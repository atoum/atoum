<?php

namespace mageekguy\atoum\asserters;

class collection extends \mageekguy\atoum\asserters\variable
{
	public function setWith($variable)
	{
		parent::setWith($variable);

		if (self::isArray($this->variable) === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not an array'), $this));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function isEmpty($failMessage = null)
	{
		if (sizeof($this->variableIsSet()->variable) == 0)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not empty'), $this));
		}
	}

	public function isNotEmpty()
	{
		if (sizeof($this->variableIsSet()->variable) > 0)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is empty'), $this));
		}
	}

	protected static function check($variable, $method)
	{
		if (self::isArray($variable) === false)
		{
			throw new \logicException('Argument of ' . $method . '() must be an array');
		}
	}

	protected static function isArray($variable)
	{
		return (is_array($variable) === true);
	}
}

?>
