<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class assertCount extends asserter
{
	public function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument #1 of assertCount was not set');
		}

		if (is_int($arguments[0]) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument #1 of assertCount must be an integer');
		}

		if (array_key_exists(1, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument #2 of assertCount was not set');
		}

		switch (true)
		{
			case $arguments[1] instanceof \countable:
				$asserter = new asserters\object();
				break;

			case $arguments[1] instanceof \iterator:
				$asserter = new asserters\iterator();
				break;

			case is_array($arguments[1]):
				$asserter = new asserters\phpArray();
				break;

			default:
				throw new exceptions\logic\invalidArgument('Argument #2 of assertCount must be countable');
		}

		try
		{
			$asserter->setWith($arguments[1])->hasSize($arguments[0]);

			$this->pass();
		}
		catch(atoum\asserter\exception $exception)
		{
			$this->fail(isset($arguments[2]) ? $arguments[2] : $exception->getMessage());
		}


		return $this;
	}
} 