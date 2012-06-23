<?php

namespace atoum\tests\units\factory;

use
	atoum,
	atoum\exceptions
;

require __DIR__ . '/../../runner.php';

class exception extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('atoum\exceptions\runtime')
		;
	}
}
