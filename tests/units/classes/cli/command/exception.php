<?php

namespace mageekguy\atoum\tests\units\cli\command;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum
;

class exception extends atoum
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\exceptions\runtime');
	}
}
