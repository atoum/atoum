<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium;

use
	\mageekguy\atoum,
	\mageekguy\atoum\tests\functional\selenium as s
;

require_once(__DIR__ . '/../../../runner.php');

class webDriver extends atoum\test
{
	public function test__construct()
	{
		$capabilities = new s\capabilities();
		$capabilities->setBrowserName(s\browser::FIREFOX);
		
		$webDriver = new s\drivers\firefox('localhost', '4444', $capabilities);

		$this->assert
			->object($webDriver->getDesiredCapabilities())
				->isInstanceOf('\mageekguy\atoum\tests\functional\selenium\capabilities')
				->isIdenticalTo($capabilities)
		;
	}
}

?>
