<?php

namespace mageekguy\atoum\tests\units\asserters\mock\exceptions;

require __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\asserters\mock\exceptions\logic as testedClass
;

class logic extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\exceptions\logic');
	}
}
