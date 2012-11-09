<?php

namespace mageekguy\atoum\tests\units\reports\realtime;

use
	mageekguy\atoum
;

require __DIR__ . '/../../../runner.php';

class tap extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->extends('mageekguy\atoum\reports\realtime')
		;
	}
}
