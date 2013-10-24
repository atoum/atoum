<?php

namespace mageekguy\atoum\tests\units\asserters\adapter\call\exceptions;

require __DIR__ . '/../../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\asserters\adapter\call\exceptions\logic as testedClass
;

class logic extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\exceptions\logic');
	}
}
