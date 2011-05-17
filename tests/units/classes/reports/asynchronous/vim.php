<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	\mageekguy\atoum,
	\mageekguy\atoum\reports\asynchronous
;

require_once(__DIR__ . '/../../../runner.php');

class vim extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\reports\asynchronous')
		;
	}

	public function test__construct()
	{
		$report = new asynchronous\vim($locale = new atoum\locale(), $adapter = new atoum\adapter());

		$this->assert
			->object($report->getLocale())->isIdenticalTo($locale)
			->object($report->getAdapter())->isIdenticalTo($adapter)
		;
	}
}

?>
