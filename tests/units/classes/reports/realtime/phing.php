<?php

namespace atoum\tests\units\reports\realtime;

use
	atoum,
	atoum\reports,
	atoum\cli\prompt,
	atoum\cli\colorizer,
	atoum\report\fields
;

require_once __DIR__ . '/../../../runner.php';

class phing extends atoum\test
{
	public function test__construct()
	{
		$this->assert
			->if($report = new reports\realtime\phing())
			->then
				->variable($report->getCodeCoverageReportPath())->isNull()
				->variable($report->getCodeCoverageReportUrl())->isNull()
				->boolean($report->durationIsShowed())->isTrue()
				->boolean($report->memoryIsShowed())->isTrue()
				->boolean($report->codeCoverageIsShowed())->isTrue()
				->boolean($report->missingCodeCoverageIsShowed())->isTrue()
				->boolean($report->progressIsShowed())->isTrue()
				->object($report->getFactory())->isInstanceOf('atoum\factory')
		  ;
	}
}
