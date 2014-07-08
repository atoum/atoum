<?php

namespace mageekguy\atoum\tests\units\reports;

require __DIR__ . '/../../runner.php';

use
	mageekguy\atoum
;

class realtime extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report');
	}

	public function testIsOverridableBy()
	{
		$this
			->if($report = new \mock\mageekguy\atoum\reports\realtime())
			->and($otherRealtimeReport = new \mock\mageekguy\atoum\reports\realtime())
			->and($otherReport = new \mock\mageekguy\atoum\report())
			->then
				->boolean($report->isOverridableBy($report))->isFalse
				->boolean($report->isOverridableBy($otherRealtimeReport))->isFalse
				->boolean($report->isOverridableBy($otherReport))->isTrue
		;
	}
}
