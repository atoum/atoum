<?php

namespace atoum\tests\units\test\assertion\manager;

require_once __DIR__ . '/../../../../runner.php';

use
	atoum
;

class exception extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubClassOf('atoum\exceptions\runtime');
	}
}
