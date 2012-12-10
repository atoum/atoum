<?php

namespace mageekguy\atoum\tests\units\reports\realtime;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields,
	mageekguy\atoum\reports\realtime\tap as testedClass
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

	public function test__construct()
	{
		$this
			->define($planField = new fields\runner\tap\plan())
			->define($eventField = new fields\test\event\tap())
			->if($report = new testedClass())
			->then
				->object($report->getLocale())->isEqualTo(new atoum\locale())
				->object($report->getAdapter())->isEqualTo(new atoum\adapter())
				->array($report->getFields())->isEqualTo(array(
						$planField,
						$eventField
					)
				)
		;
	}
}
