<?php

namespace mageekguy\atoum\tests\units\exceptions;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

require_once __DIR__ . '/../../runner.php';

class logic extends atoum\test
{
	public function test__construct()
	{
		$logicExcepion = new exceptions\logic();

		$this->assert
			->object($logicExcepion)
				->isInstanceOf('logicException')
				->isInstanceOf('mageekguy\atoum\exception')
		;
	}
}
