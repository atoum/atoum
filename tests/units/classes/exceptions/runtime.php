<?php

namespace mageekguy\atoum\tests\units\exceptions;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

require_once __DIR__ . '/../../runner.php';

class runtime extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->extends('runtimeException')
				->implements('mageekguy\atoum\exception')
		;
	}
}
