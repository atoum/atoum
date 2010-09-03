<?php

namespace mageekguy\atoum\asserters;

class object extends \mageekguy\atoum\asserters\variable
{
	public function setWith($variable, $check = true)
	{
		parent::setWith($variable);

		if ($check === true)
		{
			if (self::isObject($this->variable) === false)
			{
				$this->fail(sprintf($this->locale->_('%s is not an object'), $this));
			}
			else
			{
				$this->pass();
			}
		}

		return $this;
	}

	public function isInstanceOf($variable)
	{
		try
		{
			self::check($variable, __METHOD__);
		}
		catch (\logicException $exception)
		{
			if (self::classExists($variable) === false)
			{
				throw new \logicException('Argument of ' . __METHOD__ . '() must be a class instance or a class name');
			}
		}

		$this->variable instanceof $variable ? $this->pass() : $this->fail(sprintf($this->locale->_('%s is not an instance of %s'), $this, is_string($variable) === true ? $variable : self::toString($variable)));

		return $this;
	}

	protected static function check($variable, $method)
	{
		if (self::isObject($variable) === false)
		{
			throw new \logicException('Argument of ' . $method . '() must be a class instance');
		}
	}

	protected static function isObject($variable)
	{
		return (is_object($variable) === true);
	}

	protected static function classExists($variable)
	{
		return (class_exists($variable) === true || interface_exists($variable) === true);
	}
}

?>
