<?php

namespace mageekguy\atoum\tests\functional\selenium\drivers;

use mageekguy\atoum\tests\functional\selenium;

class htmlunit extends selenium\webDriver
{
	protected function getBrowserName()
	{
		return selenium\browser::HTMLUNIT;
	}
}

?>
