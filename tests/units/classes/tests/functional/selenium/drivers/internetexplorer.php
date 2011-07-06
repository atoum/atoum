<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium\drivers;

use
	\mageekguy\atoum,
	\mageekguy\atoum\tests\functional\selenium
;

require_once(__DIR__ . '/../../../../runner.php');

class internetexplorer extends atoum\test
{
	public function test__construct()
	{
		$internetexplorerDriver = new selenium\drivers\internetexplorer();
		
		$this->assert->string($internetexplorerDriver->getDesiredCapabilities()->getBrowserName())->isEqualTo(selenium\browser::INTERNETEXPLORER);
	}
	
	public function test__constructWithDesiredCapabilities()
	{
		$desiredCapabilities = new selenium\capabilities();
		$desiredCapabilities->setBrowserName(selenium\browser::CHROME);
		
		$this->assert->exception(function() use($desiredCapabilities) {
				$internetexplorerDriver = new selenium\drivers\internetexplorer('localhost', '4444', $desiredCapabilities);
			}
		)
			->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
			->hasMessage('Desired browser name does not math this webdriver implementation');
	}
}

?>
