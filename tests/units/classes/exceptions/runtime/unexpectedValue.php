<?php

namespace mageekguy\atoum\tests\units\exceptions\runtime;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions\runtime
;

require_once __DIR__ . '/../../../runner.php';

class unexpectedValue extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->isSubclassOf('runtimeException')
				->isSubclassOf('unexpectedValueException')
				->hasInterface('mageekguy\atoum\exception')
		;
	}
}
