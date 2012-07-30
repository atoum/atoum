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
		$this
			->if($logicExcepion = new exceptions\logic())
			->then
			->object($logicExcepion)
				->isInstanceOf('logicException')
				->isInstanceOf('mageekguy\atoum\exception')
		;
	}
}
