<?php

namespace mageekguy\atoum\asserters;

use \mageekguy\atoum\exceptions;

class float extends \mageekguy\atoum\asserters\integer
{
	public function setWith($variable, $label = null)
	{
		variable::setWith($variable, $label);

		if (self::isFloat($this->variable) === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not a float'), $this));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	protected static function check($variable, $method)
	{
		if (self::isFloat($variable) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be a float');
		}
	}

	protected static function isFloat($variable)
	{
		return (is_float($variable) === true);
	}
}

?>
