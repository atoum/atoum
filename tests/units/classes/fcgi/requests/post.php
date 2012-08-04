<?php

namespace mageekguy\atoum\tests\units\fcgi\requests;

use
	mageekguy\atoum
;

class post extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->isSubclassOf('mageekguy\atoum\fcgi\request')
		;
	}
}
