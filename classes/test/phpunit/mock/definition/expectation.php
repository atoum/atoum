<?php

namespace mageekguy\atoum\test\phpunit\mock\definition;

use mageekguy\atoum\asserters;

interface expectation
{
	public function verdict(asserters\mock $asserter);
}