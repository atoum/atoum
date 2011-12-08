<?php

namespace mageekguy\atoum\tests\units\scripts\phar;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts\phar
;

require_once __DIR__ . '/../../../runner.php';

class stub extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\scripts\runner')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(phar\stub::scriptsDirectory)->isEqualTo('scripts')
			->string(phar\stub::scriptsExtension)->isEqualTo('.php')
		;
	}
}

?>
