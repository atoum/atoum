<?php

namespace mageekguy\atoum\tests\units\asserters\adapter\call\manager;

require __DIR__ . '/../../../../../runner.php';

use atoum;

class exception extends atoum
{
	public function testClass()
	{
		$this->testedClass
			->extends('runtimeException')
			->implements('mageekguy\atoum\exception')
		;
	}
}
