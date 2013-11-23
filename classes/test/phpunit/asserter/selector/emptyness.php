<?php
namespace mageekguy\atoum\test\phpunit\asserter\selector;

use mageekguy\atoum;
use mageekguy\atoum\test\phpunit\asserter\selector;

class emptyness extends size
{
	protected function selectAsserter($value)
	{
		try
		{
			return parent::selectAsserter($value);
		}
		catch(atoum\exceptions\logic\invalidArgument $exception)
		{
			switch (true)
			{
				case is_string($value):
					return new atoum\asserters\phpString();

				default:
					throw new atoum\exceptions\logic\invalidArgument(sprintf('Cannot check if %s is empty', gettype($value)));
			}

		}
	}
} 
