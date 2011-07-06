<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium\drivers;

use
	\mageekguy\atoum,
	\mageekguy\atoum\tests\functional\selenium
;

require_once(__DIR__ . '/../../../../runner.php');

class iphone extends atoum\test
{
	public function test__construct()
	{
		$iphoneDriver = new selenium\drivers\iphone();
		
		$this->assert->string($iphoneDriver->getDesiredCapabilities()->getBrowserName())->isEqualTo(selenium\browser::IPHONE);
	}
	
	public function test__constructWithDesiredCapabilities()
	{
		$desiredCapabilities = new selenium\capabilities();
		$desiredCapabilities->setBrowserName(selenium\browser::CHROME);
		
		$this->assert->exception(function() use($desiredCapabilities) {
				$iphoneDriver = new selenium\drivers\iphone('localhost', '4444', $desiredCapabilities);
			}
		)
			->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
			->hasMessage('Desired browser name does not math this webdriver implementation');
	}
}

?>
