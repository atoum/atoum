<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class assertContains extends asserter
{
	public function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 0 of assertCount was not set');
		}

		if (array_key_exists(1, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument 1 of assertCount was not set');
		}

		if (is_object($arguments[1]) === false)
		{
			switch (true)
			{
				case is_array($arguments[1]):
					$asserter = new asserters\phpArray();
					break;

				case is_string($arguments[1]):
					$asserter = new asserters\string();
					break;

				default:
					throw new exceptions\logic\invalidArgument(sprintf('Cannot check if %s contains %s', $this->getTypeOf($arguments[1]), $arguments[0]));
			}

			try
			{
				if (is_object($arguments[0]))
				{
					$asserter->setWith($arguments[1])->strictlyContains($arguments[0]);
				}
				else
				{
					$asserter->setWith($arguments[1])->contains($arguments[0]);
				}

				$this->pass();
			}
			catch(atoum\asserter\exception $exception)
			{
				$this->fail(isset($arguments[2]) ? $arguments[2] : $exception->getMessage());
			}
		}
		else
		{
			$pass = false;
			switch (true)
			{
				case $arguments[1] instanceof \splObjectStorage:
					$pass = $arguments[1]->contains($arguments[0]);
					break;

				case $arguments[1] instanceof \traversable:
					foreach ($arguments[1] as $value)
					{
						if (
							$pass === false &&
							(
								(is_object($arguments[0]) && $value === $arguments[0]) ||
								(is_object($arguments[0]) === false && $value == $arguments[0])
							)
						)
						{
							$pass = true;
						}
					}
					break;

				default:
					throw new exceptions\logic\invalidArgument(sprintf('Cannot check if %s contains %s', $this->getTypeOf($arguments[1]), $this->getTypeOf($arguments[0])));
			}

			if ($pass)
			{
				$this->pass();
			}
			else
			{
				$this->fail(isset($arguments[2]) ? $arguments[2] : sprintf('%s does not contain %s', $this->getTypeOf($arguments[1]), $this->getTypeOf($arguments[0])));
			}
		}

		return $this;
	}
} 