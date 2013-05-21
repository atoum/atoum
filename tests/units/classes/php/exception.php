<?php

namespace mageekguy\atoum\tests\units\php;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../../runner.php';

class exception extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\exceptions\runtime');
	}
}
