<?php

namespace mageekguy\atoum\tests\units\test\interpreter;

require_once __DIR__ . '/../../runner.php';

use
	mageekguy\atoum
;

class exception extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubClassOf('mageekguy\atoum\exceptions\runtime')
		;
	}
}

?>
