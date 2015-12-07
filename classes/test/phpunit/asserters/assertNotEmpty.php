<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\phpunit\asserter
;

class assertNotEmpty extends asserter
{
	public function setWithArguments(array $arguments)
	{
		parent::setWithArguments($arguments);

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
		catch(atoum\asserter\exception $exception)
		{
			$this->fail(isset($arguments[1]) ? $arguments[1] : $exception->getMessage());
		}

		return $this;
	}

	protected function checkArguments(array $arguments, $expectedNumber = null)
	{
		return parent::checkArguments($arguments, $expectedNumber ?: 1);
	}
} 