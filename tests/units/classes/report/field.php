<?php

namespace mageekguy\atoum\tests\units\report;

use
	mageekguy\atoum,
	mageekguy\atoum\report
;

require_once __DIR__ . '/../../runner.php';

class field extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isAbstract();
	}

	public function test__construct()
	{
		$this
			->if($field = new \mock\mageekguy\atoum\report\field())
			->then
				->variable($field->getEvents())->isNull()
				->object($field->getLocale())->isEqualTo(new atoum\locale())
		;
	}
}
