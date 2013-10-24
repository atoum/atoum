<?php

namespace mageekguy\atoum\tests\units\mock\generator\method;

require __DIR__ . '/../../../../runner.php';

use
	atoum
;

class exception extends atoum
{
	public function testClass()
	{
		$this->testedClass
			->extends('exception')
			->implements('mageekguy\atoum\exception')
		;
	}
}
