<?php

namespace atoum\tests\units\exceptions\logic;

use
	atoum,
	atoum\exceptions\logic
;

require_once __DIR__ . '/../../../runner.php';

class badMethodCall extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubclassOf('logicException')
				->isSubclassOf('badMethodCallException')
				->isSubclassOf('atoum\exception')
		;
	}
}
