<?php

namespace mageekguy\atoum\tests\units\factory;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

require __DIR__ . '/../../runner.php';

class exception extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->isSubclassOf('mageekguy\atoum\exceptions\runtime')
		;
	}
}
