<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class assertContainsOnlyInstancesOf extends asserter
{
	public function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 0 of assertContainsOnlyInstancesOf was not set');
		}

		if (array_key_exists(1, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 1 of assertContainsOnlyInstancesOf was not set');
		}

		$asserter = new asserters\object();
		$failMessage = isset($arguments[2]) ? $arguments[2] : null;

		foreach ($arguments[1] as $item)
		{
			try
			{
				$asserter->setWith($item, false)->isInstanceOf($arguments[0]);
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