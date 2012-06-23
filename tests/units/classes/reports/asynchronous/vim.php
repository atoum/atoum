<?php

namespace atoum\tests\units\reports\asynchronous;

use
	atoum,
	atoum\reports\asynchronous
;

require_once __DIR__ . '/../../../runner.php';

class vim extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('atoum\reports\asynchronous');
	}

	public function test__construct()
	{
		$this
			->if($report = new asynchronous\vim())
			->then
				->object($report->getFactory())->isInstanceOf('atoum\factory')
				->object($report->getLocale())->isInstanceOf('atoum\locale')
				->object($report->getAdapter())->isInstanceOf('atoum\adapter')
			->if($factory = new atoum\factory())
			->and($factory['atoum\locale'] = $locale = new atoum\locale())
			->and($factory['atoum\adapter'] = $adapter = new atoum\adapter())
			->and($report = new asynchronous\vim($factory))
			->then
				->object($report->getFactory())->isIdenticalTo($factory)
				->object($report->getLocale())->isIdenticalTo($locale)
				->object($report->getAdapter())->isIdenticalTo($adapter)
		;
	}
}
