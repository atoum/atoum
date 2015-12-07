<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\phpunit\asserter\selector
;

class assertArrayHasKey extends assertArray
{
	public function setWithArguments(array $arguments)
	{
		parent::setWithArguments($arguments);

		try
		{
			$asserter = $this->asserterSelector->selectFor($arguments[1]);
			$asserter->hasKey($arguments[0]);

			$this->pass();
		}
		catch(atoum\asserter\exception $exception)
		{
			$this->fail(isset($arguments[2]) ? $arguments[2] : $exception->getMessage());
		}

		return $this;
	}
} 