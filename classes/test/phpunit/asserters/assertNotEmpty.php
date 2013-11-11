<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class assertNotEmpty extends asserter
{
	public function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 0 of assertEmpty was not set');
		}

		switch (true)
		{
			case is_object($arguments[0]):
				$asserter = new asserters\object();
				break;

			case is_array($arguments[0]):
				$asserter = new asserters\phpArray();
				break;

			case is_string($arguments[0]):
				$asserter = new asserters\string();
				break;

			default:
				throw new exceptions\logic\invalidArgument(sprintf('Cannot check if %s is not empty', $this->getTypeOf($arguments[0])));
		}

		try
		{
			$asserter->setWith($arguments[0])->isNotEmpty();

			$this->pass();
		}
		catch(asserter\exception $exception)
		{
			$this->fail(isset($arguments[1]) ? $arguments[1] : $exception->getMessage());
		}

		return $this;
	}
} 