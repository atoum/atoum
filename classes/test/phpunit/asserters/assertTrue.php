<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\phpunit\asserter
;

class assertTrue extends asserter
{
	public function setWithArguments(array $arguments)
	{
		parent::setWithArguments($arguments);

		try
		{
			$asserter = new asserters\boolean();
			$asserter->setWith($arguments[0])->isTrue();

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