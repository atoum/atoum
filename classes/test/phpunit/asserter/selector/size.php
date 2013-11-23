<?php
namespace mageekguy\atoum\test\phpunit\asserter\selector;

use mageekguy\atoum;
use mageekguy\atoum\test\phpunit\asserter\selector;

class size extends selector
{
	protected function selectAsserter($value)
	{
		switch (true)
		{
			case $value instanceof \iterator:
				return new atoum\asserters\iterator();

			case is_object($value):
				return new atoum\asserters\object();

			case is_array($value):
				return new atoum\asserters\phpArray();

			default:
				throw new atoum\exceptions\logic\invalidArgument(sprintf('Cannot check size of %s', gettype($value)));
		}
	}
} 