<?php

namespace atoum\tests\units\reports\asynchronous;

use
	atoum,
	atoum\report,
	ageekguy\atoum\asserter\exception,
	atoum\reports\asynchronous as reports
;

require_once __DIR__ . '/../../../runner.php';

class xunit extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('atoum\reports\asynchronous');
	}

	public function testClassConstants()
	{
		$this->string(atoum\reports\asynchronous\xunit::defaultTitle)->isEqualTo('atoum testsuite');
	}

	public function test__construct()
	{
		$this
			->if($report = new reports\xunit())
			->then
				->array($report->getFields(atoum\runner::runStart))->isEmpty()
				->object($report->getFactory())->isInstanceOf('atoum\factory')
				->object($report->getLocale())->isInstanceOf('atoum\locale')
				->object($report->getAdapter())->isInstanceOf('atoum\adapter')
			->if($factory = new atoum\factory())
			->and($factory['atoum\locale'] = $locale = new atoum\locale())
			->and($factory['atoum\adapter'] = $adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($report = new reports\xunit($factory))
			->then
				->object($report->getFactory())->isIdenticalTo($factory)
				->object($report->getLocale())->isIdenticalTo($locale)
				->object($report->getAdapter())->isIdenticalTo($adapter)
				->array($report->getFields())->isEmpty()
				->adapter($adapter)->call('extension_loaded')->withArguments('libxml')->once()
			->if($adapter->extension_loaded = false)
			->then
				->exception(function() use ($factory) {
								new reports\xunit($factory);
							}
						)
				->isInstanceOf('atoum\exceptions\runtime')
				->hasMessage('libxml PHP extension is mandatory for xunit report')
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($report = new reports\xunit())
			->then
				->object($report->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($report)
				->object($report->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($report = new reports\xunit())
			->then
				->variable($report->getTitle())->isNull()
				->castToString($report)->isEmpty()
				->string($report->handleEvent(atoum\runner::runStop, new atoum\runner())->getTitle())->isEqualTo(atoum\reports\asynchronous\xunit::defaultTitle)
				->castToString($report)->isNotEmpty()
			->if($report = new reports\xunit())
			->then
				->string($report->setTitle($title = uniqid())->handleEvent(atoum\runner::runStop, new atoum\runner())->getTitle())->isEqualTo($title)
			->if($report = new reports\xunit())
			->and($writer = new \mock\atoum\writers\file())
			->and($writer->getMockController()->write = $writer)
			->then
				->when(function() use ($report, $writer) { $report->addWriter($writer)->handleEvent(atoum\runner::runStop, new \atoum\runner()); })
					->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
		;
	}
}
