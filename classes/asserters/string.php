<?php

namespace mageekguy\atoum\asserters;

use \mageekguy\atoum\tools;

class string extends variable
{
	protected $charlist = null;

	public function getCharlist()
	{
		return $this->charlist;
	}

	public function setWith($variable, $charlist = null, $checkType = true)
	{
		$this->charlist = $charlist;

		parent::setWith($variable);

		if ($checkType === true)
		{
			if (self::isString($this->variable) === false)
			{
				$this->fail(sprintf($this->locale->_('%s is not a string'), $this));
			}
			else
			{
				$this->pass();
			}
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

	public function toString($mixed)
	{
		return (is_string($mixed) === false ? parent::toString($mixed) : sprintf($this->locale->_('string(%s) \'%s\''), strlen($mixed), addcslashes($mixed, $this->charlist)));
	}

	public function isEqualTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		if ($this->variableIsSet()->variable == $variable)
		{
			return $this->pass();
		}
		else
		{
			$diff = new tools\diff($this->variable, $variable);

			$this->fail($failMessage !== null ? $failMessage : $this->locale->_('strings are not equals:') . PHP_EOL . $diff);
		}
	}

	protected function setWithArguments(array $arguments)
	{
		if (isset($arguments[1]) === true)
		{
			$this->charlist = $arguments[1];
		}

		return parent::setWithArguments($arguments);
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
