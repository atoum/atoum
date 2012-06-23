<?php

namespace mageekguy\atoum\tests\units\exceptions;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
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
				->isInstanceOf('mageekguy\atoum\exception')
		;
	}
}
