<?php

namespace atoum\tests\units\test;

require_once __DIR__ . '/../../runner.php';

use
	atoum
;

class engine extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isAbstract();
	}
}
