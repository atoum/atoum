<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum\asserters\object,
	mageekguy\atoum\exceptions
;

class assertInstanceOf extends object
{
	public function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 0 of assertInstanceOf was not set');
		}

		if (array_key_exists(1, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 1 of assertInstanceOf was not set');
		}

		$failMessage = isset($arguments[2]) ? $arguments[2] : null;

		try
		{
			static::check($arguments[1], 'assertInstanceOf');
		}
		catch(exceptions\logic $exception)
		{
			return $this->pass();
		}

		try
		{
			return parent::setWithArguments(array($arguments[1]))->isInstanceOf($arguments[0], $failMessage);
		}
		catch(exceptions\logic $exception)
		{
			return $this->fail($failMessage ?: sprintf('%s is not an instance of %s', $this->getTypeOf($arguments[1]), $arguments[0]));
		}
	}
} 