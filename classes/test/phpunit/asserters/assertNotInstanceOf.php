<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\phpunit\asserter
;

class assertNotInstanceOf extends asserter
{
	public function setWithArguments(array $arguments)
	{
		parent::setWithArguments($arguments);

		$asserter = new atoum\asserters\object();

		try
		{
			try
			{
				$asserter->setWith($arguments[1]);
			}
			catch(atoum\asserter\exception $exception) {}

			$asserter->isNotInstanceOf($arguments[0]);

			$this->pass();
		}
		catch(atoum\exceptions\logic $exception)
		{
			$this->pass();
		}
		catch(atoum\asserter\exception $exception)
		{
			$this->fail(isset($arguments[2]) ? $arguments[2] : $exception->getMessage());
		}

		return $this;
	}
} 