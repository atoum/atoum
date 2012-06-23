<?php

namespace atoum\tests\units\exceptions\runtime;

use
	atoum,
	atoum\exceptions\runtime
;

require_once __DIR__ . '/../../../runner.php';

class unexpectedValue extends atoum\test
{
	public function test__construct()
	{
		$unexpectedValueException = new runtime\unexpectedValue();

		$this->assert
			->object($unexpectedValueException)
				->isInstanceOf('runtimeException')
				->isInstanceOf('unexpectedValueException')
				->isInstanceOf('atoum\exception')
		;
	}
}
