<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
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

	public function isNearlyEqualTo($value, $epsilon = null, $failMessage = null)
	{
		static::check($value, __METHOD__);

		//see http://www.floating-point-gui.de/errors/comparison/ for more informations

		$originalValue = abs($this->valueIsSet()->value);
		$value = abs($value);
		$diff = abs($originalValue - $value);

		if ($epsilon === null) {
			$epsilon = pow(10, - ini_get('precision'));
		}

		switch (true)
		{
			case $originalValue == $value:
			case $originalValue * $value == 0 && $diff < pow($epsilon, 2):
			case $diff / ($originalValue + $value) < $epsilon:
				return $this;

			default:
				$diff = new diffs\variable();

				$this->fail(
					($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not nearly equal to %s with epsilon %s'), $this, $this->getTypeOf($value), $epsilon)) .
					PHP_EOL .
					$diff->setReference($value)->setData($this->value)
				);
		}
	}
}
