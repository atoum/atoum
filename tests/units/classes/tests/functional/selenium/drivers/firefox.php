<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium\drivers;

use
	mageekguy\atoum,
	mageekguy\atoum\tests\functional\selenium
;

require_once(__DIR__ . '/../../../../runner.php');

class firefox extends atoum\test
{
	public function test__construct()
	{
		$firefoxDriver = new selenium\drivers\firefox();

		$this->assert->string($firefoxDriver->getDesiredCapabilities()->getBrowserName())->isEqualTo(selenium\browser::FIREFOX);
	}

	public function test__constructWithDesiredCapabilities()
	{
		$desiredCapabilities = new selenium\capabilities();
		$desiredCapabilities->setBrowserName(selenium\browser::CHROME);

		$this->assert->exception(function() use($desiredCapabilities) {
				$firefoxDriver = new selenium\drivers\firefox('localhost', '4444', $desiredCapabilities);
			}
		)
			->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
			->hasMessage('Desired browser name does not math this webdriver implementation');
	}
}

?>
