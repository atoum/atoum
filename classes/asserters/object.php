<?php

namespace mageekguy\atoum\asserters;

use \mageekguy\atoum\exceptions;

class object extends \mageekguy\atoum\asserters\variable
{
	public function setWith($variable, $label = null, $checkType = true)
	{
		parent::setWith($variable, $label);

		if ($checkType === true)
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
				throw new exceptions\logic('Argument of ' . __METHOD__ . '() must be a class instance or a class name');
			}
		}

		$this->variable instanceof $variable ? $this->pass() : $this->fail(sprintf($this->locale->_('%s is not an instance of %s'), $this, is_string($variable) === true ? $variable : $this->toString($variable)));

		return $this;
	}

	public function hasSize($size, $failMessage = null)
	{
		if (sizeof($this->variableIsSet()->variable) == $size)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s has not size %d'), $this, $size));
		}
	}

	public function isEmpty($failMessage = null)
	{
		if (sizeof($this->variable) == 0)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s has size %d'), $this, sizeof($this->variable)));
		}
	}

	protected function variableIsSet($message = 'Object is undefined')
	{
		return parent::variableIsSet($message);
	}

	protected static function check($variable, $method)
	{
		if (self::isObject($variable) === false)
		{
			throw new exceptions\logic('Argument of ' . $method . '() must be a class instance');
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
