<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class assertSameSize extends asserter
{
	public function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument #1 of assertSameSize was not set');
		}

		if (self::checkCountable($arguments[0]) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument #1 of assertSameSize must be countable');
		}

		if (array_key_exists(1, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument #2 of assertSameSize was not set');
		}

		if (self::checkCountable($arguments[1]) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument #2 of assertSameSize must be countable');
		}

		$asserter = new asserters\sizeOf();

		try
		{
			$asserter->setWith($arguments[1])->isEqualTo(sizeof($arguments[0]));

			$this->pass();
		}
		catch(atoum\asserter\exception $exception)
		{
			$this->fail(isset($arguments[2]) ? $arguments[2] : $exception->getMessage());
		}


		return $this;
	}

	protected static function checkCountable($value)
	{
		return (is_array($value) || ($value instanceof \countable));
	}
} 