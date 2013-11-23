<?php

namespace mageekguy\atoum\test\phpunit;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

abstract class asserter extends atoum\asserter
{
	public function setWithArguments(array $arguments)
	{
		return $this->checkArguments($arguments);
	}

	protected function checkArguments(array $arguments, $expectedNumber = null)
	{
		$expectedNumber = $expectedNumber ?: 2;

		for ($argument = 0; $argument < $expectedNumber; $argument++)
		{
			if (array_key_exists($argument, $arguments) === false)
			{
				throw new exceptions\logic\invalidArgument(sprintf($this->locale->_('Argument #%d of %s was not set'), ($argument + 1), $this->getName()));
			}
		}

		return $this;
	}

	protected function getName()
	{
		return preg_replace('#^.*\\\#', '', get_class($this));
	}
}
