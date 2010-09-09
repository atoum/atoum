<?php

namespace mageekguy\atoum\asserters;

class string extends \mageekguy\atoum\asserters\variable
{
	public function setWith($variable)
	{
		parent::setWith($variable);

		if (self::isString($this->variable) === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not a string'), $this));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function isEmpty($failMessage = null)
	{
		return $this->isEqualTo('', $failMessage);
	}

	public function match($pattern, $failMessage = null)
	{
		preg_match($pattern, $this->variable) === 1 ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s does not match %s'), $this, $pattern));

		return $this;
	}

	protected static function check($variable, $method)
	{
		if (self::isString($variable) === false)
		{
			throw new \logicException('Argument of ' . $method . '() must be a string');
		}
	}

	protected static function isString($variable)
	{
		return (is_string($variable) === true);
	}
}

?>
