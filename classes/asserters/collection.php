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

	public function isEmpty()
	{
		sizeof($this->variable) == 0 ? $this->pass() : $this->fail(sprintf($this->locale->_('%s is not empty'), $this));

		return $this;
	}

	public function isNotEmpty()
	{
		sizeof($this->variable) > 0 ? $this->pass() : $this->fail(sprintf($this->locale->_('%s is empty'), $this));

		return $this;
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
