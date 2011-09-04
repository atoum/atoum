<?php

namespace mageekguy\atoum\tests\units\report;

use
	mageekguy\atoum,
	mageekguy\atoum\report
;

require_once __DIR__ . '/../../runner.php';

class field extends atoum\test
{
	public function test__construct()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\report\field')
		;

		$field = new \mock\mageekguy\atoum\report\field();

		$this->assert
			->object($field->getLocale())->isInstanceOf('mageekguy\atoum\locale')
		;

		$field = new \mock\mageekguy\atoum\report\field($locale = new atoum\locale());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
		;
	}
}

?>
