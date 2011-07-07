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
	
	public function testAtoum()
	{
		$adapter = new atoum\test\adapter();
		$adapter->curl_init = function() {};
		$adapter->curl_setopt = function() {};
		$adapter->curl_getinfo = function() {};
		$adapter->curl_close = function() {};
		$adapter->curl_errno = function() {};
		$adapter->curl_exec = '{"name":"title", "value":"www.mageekbox.net"}';
		
		$webDriver = new d\firefox('localhost', '4444', null, $adapter);
		$webDriver->get('http://www.atoum.org');
		
		$this->assert
			->string($webDriver->getTitle())
				->isEqualTo('www.mageekbox.net');
	}
	
	public function testGoogle()
	{
		$adapter = new atoum\test\adapter();
		$adapter->curl_init = function() {};
		$adapter->curl_setopt = function() {};
		$adapter->curl_getinfo = function() {};
		$adapter->curl_close = function() {};
		$adapter->curl_errno = function() {};
		$adapter->curl_exec = '{"name":"title", "value":"Google"}';
		
		$webDriver = new d\firefox('localhost', '4444', null, $adapter);
		$webDriver->get('http://www.google.com');
		
		$this->assert
			->string($webDriver->getTitle())
				->isEqualTo('Google');
	}
}

?>