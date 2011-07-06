<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium\drivers;

use
	\mageekguy\atoum,
	\mageekguy\atoum\tests\functional\selenium
;

require_once(__DIR__ . '/../../../../runner.php');

class htmlunit extends atoum\test
{
	public function test__construct()
	{
		$htmlunitDriver = new selenium\drivers\htmlunit();
		
		$this->assert->string($htmlunitDriver->getDesiredCapabilities()->getBrowserName())->isEqualTo(selenium\browser::HTMLUNIT);
	}
	
	public function test__constructWithDesiredCapabilities()
	{
		$desiredCapabilities = new selenium\capabilities();
		$desiredCapabilities->setBrowserName(selenium\browser::CHROME);
		
		$this->assert->exception(function() use($desiredCapabilities) {
				$htmlunitDriver = new selenium\drivers\htmlunit('localhost', '4444', $desiredCapabilities);
			}
		)
			->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
			->hasMessage('Desired browser name does not math this webdriver implementation');
	}
}

?>
