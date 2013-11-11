<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class assertNotEquals extends assertEquals
{
	public function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument #1 of assertNotEquals was not set');
		}

		if (array_key_exists(1, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument #2 of assertNotEquals was not set');
		}

		$exception = null;
		try
		{
			parent::setWithArguments($arguments);
		}
		catch(atoum\asserter\exception $exception)
		{
			$this->pass();
		}

		if ($exception === null)
		{
			$this->fail(isset($arguments[2]) ? $arguments[2] : sprintf('%s is equal to %s', $this->getTypeOf($arguments[0]), $this->getTypeOf($arguments[1])));
		}

		return $this;
	}
} 