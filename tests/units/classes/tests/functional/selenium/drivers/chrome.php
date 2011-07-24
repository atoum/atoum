<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium\drivers;

use
	mageekguy\atoum,
	mageekguy\atoum\tests\functional\selenium
;

require_once(__DIR__ . '/../../../../runner.php');

class chrome extends atoum\test
{
	public function test__construct()
	{
		$chromeDriver = new selenium\drivers\chrome();

		$this->assert->string($chromeDriver->getDesiredCapabilities()->getBrowserName())->isEqualTo(selenium\browser::CHROME);
	}

	public function test__constructWithDesiredCapabilities()
	{
		$desiredCapabilities = new selenium\capabilities();
		$desiredCapabilities->setBrowserName(selenium\browser::FIREFOX);

		$this->assert->exception(function() use($desiredCapabilities) {
				$chromeDriver = new selenium\drivers\chrome('localhost', '4444', $desiredCapabilities);
			}
		)
			->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
			->hasMessage('Desired browser name does not math this webdriver implementation');
	}
}

?>
