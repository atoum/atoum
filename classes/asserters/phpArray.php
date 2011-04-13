<?php

namespace mageekguy\atoum\asserters;

use
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\tools\diffs
;

class phpArray extends \mageekguy\atoum\asserters\variable
{
	public function setWith($variable, $label = null)
	{
		parent::setWith($variable, $label);

		if (self::isArray($this->variable) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not an array'), $this));
		}
		else
		{
			$this->pass();
		}

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
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has not size %d'), $this, $size));
		}
	}

	public function isEmpty($failMessage = null)
	{
		if (sizeof($this->variableIsSet()->variable) == 0)
		{
			return $this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(
				($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not empty'), $this))
			);
		}
	}

	public function isNotEmpty($failMessage = null)
	{
		if (sizeof($this->variableIsSet()->variable) > 0)
		{
			return $this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(
				($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is empty'), $this))
			);
		}
	}

	public function contain($variable, $failMessage = null)
	{
		if (in_array($variable, $this->variableIsSet()->variable) === true)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not contain %s'), $this, $this->toString($variable)));
		}
	}

	protected static function check($variable, $method)
	{
		if (self::isArray($variable) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be an array');
		}
	}

	protected static function isArray($variable)
	{
		return (is_array($variable) === true);
	}
}

?>
