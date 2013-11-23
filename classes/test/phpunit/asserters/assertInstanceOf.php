<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\phpunit\asserter
;

class assertInstanceOf extends asserter
{
	public function setWithArguments(array $arguments)
	{
		parent::setWithArguments($arguments);

		$asserter = new asserters\object();

		try
		{
			$asserter->setWith($arguments[1])->isInstanceOf($arguments[0]);

			$this->pass();
		}
		catch(atoum\asserter\exception $exception)
		{
			$this->fail(isset($arguments[2]) ? $arguments[2] : $exception->getMessage());
		}
		catch(atoum\exceptions\logic $exception)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not an instance of %s'), is_string($arguments[1]) === true ? $arguments[1] : $this->getTypeOf($arguments[1]), $arguments[0]));
		}

		return $this;
	}
} 