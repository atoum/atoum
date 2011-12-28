<?php

namespace mageekguy\atoum\tests\units\reports\realtime;

use
	mageekguy\atoum,
	mageekguy\atoum\reports,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields
;

require_once __DIR__ . '/../../../runner.php';

class phing extends atoum\test
{
	public function test__construct()
	{
		$this->assert
			->if($report = new reports\realtime\phing())
			->then
				->variable($report->getCodecoveragereportpath())
                    ->isNull()
                ->variable($report->getCodecoveragereporturl())
                    ->isNull()
                ->boolean($report->getShowDuration())
                    ->isTrue()
                ->boolean($report->getShowMemory())
                    ->isTrue()
                ->boolean($report->getShowMissingCodeCoverage())
                    ->isTrue()
                ->boolean($report->getShowCodeCoverage())
                    ->isTrue()
                ->boolean($report->getShowProgress())
                    ->isTrue()
        ;
	}
}
?>