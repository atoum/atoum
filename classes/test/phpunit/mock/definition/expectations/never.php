<?php

namespace mageekguy\atoum\test\phpunit\mock\definition\expectations;

use mageekguy\atoum\asserters;
use mageekguy\atoum\test\phpunit\mock\definition\expectation;

class never implements expectation
{
	public function verdict(asserters\mock $asserter)
	{
		return $asserter->never();
	}
}