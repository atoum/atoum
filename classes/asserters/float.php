<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class float extends \mageekguy\atoum\asserters\integer
{
	public function setWith($value, $label = null)
	{
		variable::setWith($value, $label);

		if (self::isFloat($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a float'), $this));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	protected static function check($value, $method)
	{
		if (self::isFloat($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be a float');
		}
	}

	protected static function isFloat($value)
	{
		return (is_float($value) === true);
	}
}
