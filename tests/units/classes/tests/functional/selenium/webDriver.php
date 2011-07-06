<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium;

use
	\mageekguy\atoum,
	\mageekguy\atoum\tests\functional\selenium as s,
	\mageekguy\atoum\tests\functional\selenium\drivers as d
;

require_once(__DIR__ . '/../../../runner.php');

class webDriver extends atoum\test
{
	public function test__construct()
	{
		$capabilities = new s\capabilities();
		$capabilities->setBrowserName(s\browser::FIREFOX);
		
		$webDriver = new d\firefox('localhost', '4444', $capabilities);

		$this->assert
			->object($webDriver->getDesiredCapabilities())
				->isInstanceOf('\mageekguy\atoum\tests\functional\selenium\capabilities')
				->isIdenticalTo($capabilities)
		;
	}
/*	
	public function testAtoum()
	{
		$webDriver = new d\firefox();
		$webDriver->get('http://www.atoum.org');
		
		$this->assert
			->string($webDriver->getTitle())
				->isEqualTo('www.mageekbox.net');
	}
	
	public function testGoogle()
	{
		$webDriver = new d\firefox();
		$webDriver->get('http://www.google.com');
		
		$this->assert
			->string($webDriver->getTitle())
				->isEqualTo('Google');
	}
*/
}

?>
