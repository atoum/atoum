<?php

namespace mageekguy\atoum\tests\units\mock\streams;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../../../runner.php';

class file extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\mock\stream');
	}
}
