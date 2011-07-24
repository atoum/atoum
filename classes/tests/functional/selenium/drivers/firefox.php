<?php

namespace mageekguy\atoum\tests\functional\selenium\drivers;

use
	mageekguy\atoum\tests\functional\selenium
;

class firefox extends selenium\webDriver
{
	protected function getBrowserName()
	{
		return selenium\browser::FIREFOX;
	}
}

?>
