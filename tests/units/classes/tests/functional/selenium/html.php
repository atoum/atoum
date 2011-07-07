<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium;

use
	\mageekguy\atoum,
	\mageekguy\atoum\tests\functional\selenium as s,
	\mageekguy\atoum\tests\functional\selenium\drivers as d
;

require_once(__DIR__ . '/../../../runner.php');

class html extends atoum\test
{
	public function testUnableToGetTitleIfWebDriverIsNotSet()
	{
		$html = new s\html('http://www.atoum.org');
		
		$this->assert
			->exception(function() use($html) {
						$html->getTitle();
					}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('webDriver must be set');
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
		
		$atoumHomePage = $this->getHtml('http://www.atoum.org')
							  ->with(new d\firefox('localhost', '4444', null, $adapter));
		
		$this->assert
			->html($atoumHomePage)
				->hasTitle('www.mageekbox.net');
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
		
		$googleHomePage = $this->getHtml('http://www.google.com')
							  ->with(new d\firefox('localhost', '4444', null, $adapter));
		
		$this->assert
			->html($googleHomePage)
				->hasTitle('Google');
	}
}

?>
