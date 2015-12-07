<?php

namespace mageekguy\atoum\test\phpunit\mock;

use
	mageekguy\atoum\mock
;

interface aggregator extends mock\aggregator
{
	public function getMockDefinition();
	public function expects($expectation);
}
