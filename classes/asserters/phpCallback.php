<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class phpCallback extends asserters\variable
{
	public function setWith($value, $label = null)
	{
		parent::setWith($value, $label);

		if (self::isCallback($this->value) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a callback'), $this));
		}
	}

	protected static function check($value, $method)
	{
		if (self::isCallback($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be a valid callback');
		}
	}

	protected static function isCallback($value)
	{
		return (is_callable($value) === true);
	}
}

?>
