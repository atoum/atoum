<?php

namespace mageekguy\atoum\tests\units\exceptions\logic;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions\logic
;

require_once __DIR__ . '/../../../runner.php';

class invalidArgument extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->extends('logicException')
				->extends('invalidArgumentException')
				->implements('mageekguy\atoum\exception')
		;
	}
}
