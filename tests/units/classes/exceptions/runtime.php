<?php

namespace atoum\tests\units\exceptions;

use
	atoum,
	atoum\exceptions
;

require_once __DIR__ . '/../../runner.php';

class runtime extends atoum\test
{
	public function test__construct()
	{
		$runtimeExcepion = new exceptions\runtime();

		$this->assert
			->object($runtimeExcepion)
				->isInstanceOf('runtimeException')
				->isInstanceOf('atoum\exception')
		;
	}
}
