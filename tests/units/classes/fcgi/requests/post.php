<?php

namespace mageekguy\atoum\tests\units\fcgi\requests;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../../../runner.php';

class post extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->isSubclassOf('mageekguy\atoum\fcgi\request')
		;
	}
}
