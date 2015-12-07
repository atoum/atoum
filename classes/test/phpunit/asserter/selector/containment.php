<?php
namespace mageekguy\atoum\test\phpunit\asserter\selector;

use mageekguy\atoum;
use mageekguy\atoum\test\phpunit\asserter\selector;

class containment extends selector
{
	protected function selectAsserter($value)
	{
		switch (true)
		{
			case is_object($value):
				return new atoum\asserters\castToArray();

			case is_array($value):
				return new atoum\asserters\phpArray();

			case is_string($value):
				return new atoum\asserters\phpString();

			default:
				throw new atoum\exceptions\logic\invalidArgument(sprintf('Cannot check containment in %s values', gettype($value)));
		}
	}
} 
