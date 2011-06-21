<?php

namespace mageekguy\atoum\tests\functional\selenium\drivers;

use
	mageekguy\atoum\tests\functional\selenium,
	mageekguy\atoum\exceptions\logic
;

class firefox extends selenium\webDriver
{
	public function __construct($host = 'localhost', $port = '4444', selenium\capabilities $desiredCapabilities = null)
	{
		$browserName = selenium\browser::FIREFOX;
		
		if ($desiredCapabilities == null)
		{
			$desiredCapabilities = new selenium\capabilities();
			$desiredCapabilities->setBrowserName($browserName);
		}
		else if ($desiredCapabilities->getBrowserName() != $browserName)
		{
			throw new logic\invalidArgument('Desired browser name does not math this webdriver implementation');
		}
		
		parent::__construct($host, $port, $desiredCapabilities);
	}
}

?>
