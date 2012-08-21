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

    /**
     * Test if float is nearly equal to an other one.
     *
     * Example:
     * You want to test if 101 is nearly equal to 100
     * you'll just have to pass an epsilon 0.01 or higher
     *
     * @param float       $value       value to test
     * @param float       $epsilon     epsilon, (ex: 0.01)
     * @param string|null $failMessage message if fail
     * @link http://www.floating-point-gui.de/errors/comparison/
     *
     * @return \mageekguy\atoum\asserters\float
     */
	public function isNearlyEqualTo($value, $epsilon = null, $failMessage = null)
	{
		static::check($value, __METHOD__);

		if (null === $epsilon) {
			$epsilon = pow(10, -ini_get('precision'));
		}

		$originalValue = abs($this->valueIsSet()->value);
		$value         = abs($value);
		$diff          = abs($originalValue - $value);

		if ($originalValue == $value) {
			return $this;
		} elseif ($originalValue * $value == 0) {
			if($diff < ($epsilon * $epsilon)) {
				return $this;
			}
		} else {
			if ($diff / ($originalValue + $value) < $epsilon) {
				return $this;
			}
		}

		$diff = new diffs\variable();

		$this->fail(
			($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not nearly equal to %s with epsilon %s'), $this, $this->getTypeOf($value), $epsilon)) .
			PHP_EOL .
			$diff->setReference($value)->setData($this->value)
		);
	}
}
