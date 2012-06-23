<?php

namespace atoum\tests\units\exceptions\logic;

use
	atoum,
	atoum\exceptions\logic
;

require_once __DIR__ . '/../../../runner.php';

class invalidArgument extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubclassOf('logicException')
				->isSubclassOf('invalidArgumentException')
				->isSubclassOf('atoum\exception')
		;
	}
}
