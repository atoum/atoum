<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class assertContainsOnly extends asserter
{
	public function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 0 of assertContainsOnly was not set');
		}

		if (array_key_exists(1, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 1 of assertContainsOnly was not set');
		}

		$failMessage = isset($arguments[2]) ? $arguments[2] : null;

		foreach ($arguments[1] as $item)
		{
			try
			{
				switch ($arguments[0])
				{
					case 'integer':
						$asserter = new asserters\integer();

						$asserter->setWith($item, false);
						break;

					default:
						$asserter = new asserters\object();

						$asserter->setWith($item)->isInstanceOf($arguments[0]);
						break;
				}
			}
			catch(atoum\asserter\exception $exception)
			{
				$this->fail($failMessage ?: $exception->getMessage());
			}
		}

		$this->pass();

		return $this;
	}
} 