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
		parent::setWithArguments($arguments);

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