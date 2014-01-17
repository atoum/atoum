<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
;

class float extends asserters\integer
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

	public function isNearlyEqualTo($value, $epsilon = null, $failMessage = null)
	{
		static::check($value, __FUNCTION__);

		if ($this->valueIsSet()->value !== $value)
		{
			// see http://www.floating-point-gui.de/errors/comparison/ for more informations
			$absValue = abs($value);
			$absCurrentValue = abs($this->value);
			$offset = abs($absCurrentValue - $absValue);
			$offsetIsNaN = is_nan($offset);

			if ($offsetIsNaN === false && $epsilon === null)
			{
				$epsilon = pow(10, - ini_get('precision'));
			}

			switch (true)
			{
				case $offsetIsNaN === true:
				case $offset / ($absCurrentValue + $absValue) >= $epsilon:
				case $absCurrentValue * $absValue == 0 && $offset >= pow($epsilon, 2):
					$diff = new diffs\variable();

					$this->fail(
						($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not nearly equal to %s with epsilon %s'), $this, $this->getTypeOf($value), $epsilon)) .
						PHP_EOL .
						$diff->setExpected($value)->setActual($this->value)
					);
				}
		}

		return $this;
	}

	public function isZero($failMessage = null)
	{
		return $this->isEqualTo(0.0, $failMessage);
	}

	protected static function check($value, $method)
	{
		if (self::isFloat($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . __CLASS__ . '::' . $method . '() must be a float');
		}
	}

	protected static function isFloat($value)
	{
		return (is_float($value) === true);
	}
}
