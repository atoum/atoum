<?php

namespace mageekguy\atoum\tests\units\test\assertion\manager;

require_once __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum
;

class exception extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubClassOf('mageekguy\atoum\exceptions\runtime');
	}
}
