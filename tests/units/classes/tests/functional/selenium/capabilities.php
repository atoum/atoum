<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium;

use
	\mageekguy\atoum,
	\mageekguy\atoum\tests\functional\selenium as s
;

require_once(__DIR__ . '/../../../runner.php');

class capabilities extends atoum\test
{
	public function testAccessorsMutators()
	{
		$capabilities = new s\capabilities();
		
		$this->assert->variable($capabilities->getBrowserName())->isNull();
		$this->assert->variable($capabilities->getPlatform())->isNull();
		$this->assert->variable($capabilities->getVersion())->isNull();
		$this->assert->variable($capabilities->isHandlingAlerts())->isNull();
		$this->assert->variable($capabilities->isJavascriptEnabled())->isNull();
		$this->assert->variable($capabilities->isTakingScreenshot())->isNull();
		$this->assert->variable($capabilities->hasNativeEvents())->isNull();
		
		$capabilities->setBrowserName(s\browser::FIREFOX);
		$capabilities->setPlatform(s\platform::LINUX);
		$capabilities->setVersion('4');
		$capabilities->setHandlingAlerts(true);
		$capabilities->setJavascriptEnabled(true);
		$capabilities->setTakingScreenshot(true);
		$capabilities->setNativeEvents(false);
		
		$this->assert->string($capabilities->getBrowserName())->isEqualTo(s\browser::FIREFOX);
		$this->assert->string($capabilities->getPlatform())->isEqualTo(s\platform::LINUX);
		$this->assert->string($capabilities->getVersion())->isEqualTo('4');
		$this->assert->boolean($capabilities->isHandlingAlerts())->isTrue();
		$this->assert->boolean($capabilities->isJavascriptEnabled())->isTrue();
		$this->assert->boolean($capabilities->isTakingScreenshot())->isTrue();
		$this->assert->boolean($capabilities->hasNativeEvents())->isFalse();
	}
	
	public function test__toStringWhenSomeValuesAreNotSet()
	{
		$capabilities = new s\capabilities();
		$capabilities->setBrowserName(s\browser::HTMLUNIT);
		
		$this->assert->string((string)$capabilities)->isEqualTo('{desiredCapabilities:{browserName: \'' . s\browser::HTMLUNIT . '\'}}');
	}
	
	public function test__toStringWhenSomeValuesAreNotSet2()
	{
		$capabilities = new s\capabilities();
		$capabilities->setPlatform(s\platform::MAC);
		$capabilities->setTakingScreenshot(true);
		
		$this->assert->string((string)$capabilities)->isEqualTo('{desiredCapabilities:{platform: \'' . s\platform::MAC . '\', takesScreenshot: true}}');
	}
	
	public function test__toStringWhenNoValuesAreSet()
	{
		$capabilities = new s\capabilities();
		
		$this->assert->string((string)$capabilities)->isEqualTo('{desiredCapabilities:{}}');
	}
}

?>
