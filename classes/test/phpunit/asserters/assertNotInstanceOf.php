<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum\asserters\object,
	mageekguy\atoum\exceptions
;

class assertNotInstanceOf extends object
{
	public function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 0 of assertNotInstanceOf was not set');
		}

		if (array_key_exists(1, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 1 of assertNotInstanceOf was not set');
		}

		$failMessage = isset($arguments[2]) ? $arguments[2] : null;

		try
		{
			static::check($arguments[1], 'assertNotInstanceOf');

			return parent::setWithArguments(array($arguments[1]))->isNotInstanceOf($arguments[0], $failMessage);
		}
		catch(exceptions\logic $exception)
		{
			return $this->pass();
		}
	}
} 