<?php

namespace mageekguy\atoum\tests\units\exceptions\runtime;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions\runtime
;

require_once __DIR__ . '/../../../runner.php';

class unexpectedValue extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($unexpectedValueException = new runtime\unexpectedValue())
			->then
				->object($unexpectedValueException)
					->isInstanceOf('runtimeException')
					->isInstanceOf('unexpectedValueException')
					->isInstanceOf('mageekguy\atoum\exception')
		;
	}
}
