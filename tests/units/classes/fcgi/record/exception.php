<?php

namespace mageekguy\atoum\tests\units\fcgi\record;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../../../runner.php';

class exception extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\exceptions\runtime');
	}
}
