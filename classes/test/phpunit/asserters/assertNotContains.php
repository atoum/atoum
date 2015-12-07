<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\phpunit\asserter\selector
;

class assertNotContains extends assertContains
{
	public function setWithArguments(array $arguments)
	{
		$this->checkArguments($arguments);

		try
		{
			$asserter = $this->asserterSelector->selectFor($arguments[1]);
		}
		catch(atoum\asserter\exception $exception)
		{
			throw new atoum\exceptions\logic\invalidArgument(sprintf('Cannot check containment in object(%s)', get_class($arguments[1])));
		}

		try
		{
			if (is_object($arguments[0]))
			{
				$asserter->strictlyNotContains($arguments[0]);
			}
			else
			{
				$asserter->notContains($arguments[0]);
			}

			$this->pass();
		}
		catch(atoum\asserter\exception $exception)
		{
			$this->fail(isset($arguments[2]) ? $arguments[2] : sprintf('%s contains %s', $this->getTypeOf($arguments[1]), $this->getTypeOf($arguments[0])));
		}

		return $this;
	}
} 