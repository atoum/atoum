<?php

namespace atoum\tests\units\report;

use
	atoum,
	atoum\report
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
			->if($field = new \mock\atoum\report\field())
			->then
				->variable($field->getEvents())->isNull()
				->object($field->getLocale())->isEqualTo(new atoum\locale())
		;
	}
}
