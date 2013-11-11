<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class assertArrayHasKey extends asserter
{
	public function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 0 of assertArrayHasKey was not set');
		}

		if (array_key_exists(1, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 1 of assertArrayHasKey was not set');
		}

		if ($arguments[1] instanceof \arrayAccess)
		{
			if (array_key_exists(1, $arguments) === false)
			{
				$this->fail(isset($arguments[2]) ? $arguments[2] : sprintf($this->getLocale()->_('%s has no key %s'), $this, $this->getTypeOf($key)));
			}
			else
			{
				$this->pass();
			}
		}
		else
		{
			$asserter = new asserters\phpArray();

			try
			{
				$asserter->setWith($arguments[1])->hasKey($arguments[0]);

				$this->pass();
			}
			catch(atoum\asserter\exception $exception)
			{
				$this->fail(isset($arguments[2]) ? $arguments[2] : $exception->getMessage());
			}
		}

		return $this;
	}
} 