<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
;

class phpArray extends asserters\variable
{
	public function setWith($value, $label = null)
	{
		parent::setWith($value, $label);

		if (self::isArray($this->value) === false)
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
		if (sizeof($this->valueIsSet()->value) == $size)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s has not size %d'), $this, $size));
		}

		return $this;
	}

	public function isEmpty($failMessage = null)
	{
		if (sizeof($this->valueIsSet()->value) == 0)
		{
			$this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not empty'), $this)));
		}

		return $this;
	}

	public function isNotEmpty($failMessage = null)
	{
		if (sizeof($this->valueIsSet()->value) > 0)
		{
			$this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is empty'), $this)));
		}

		return $this;
	}

	public function contains($value, $failMessage = null)
	{
		if (in_array($value, $this->valueIsSet()->value) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s does not contain %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	public function notContains($value, $failMessage = null)
	{
		if (in_array($value, $this->valueIsSet()->value) === false)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s contains %s'), $this, $this->getTypeOf($value)));
		}

		return $this;
	}

	protected static function check($value, $method)
	{
		if (self::isArray($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be an array');
		}
	}

	protected static function isArray($value)
	{
		return (is_array($value) === true);
	}
}

?>
