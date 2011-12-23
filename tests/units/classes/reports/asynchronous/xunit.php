<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	ageekguy\atoum\asserter\exception,
	mageekguy\atoum\reports\asynchronous as reports
;

require_once __DIR__ . '/../../../runner.php';

class xunit extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\reports\asynchronous')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(atoum\reports\asynchronous\xunit::defaultTitle)->isEqualTo('atoum testsuite')
		;
	}

	public function test__construct()
	{
		$report = new reports\xunit();

		$this->assert
			->array($report->getFields(atoum\runner::runStart))->isEmpty()
			->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
		;

		$adapter = new atoum\test\adapter();
		$adapter->extension_loaded = function($extension) { return true; };

		$this->assert
			->when(function() use (& $report, $adapter) { $report = new reports\xunit($adapter); })
				->array($report->getFields())->isEmpty()
				->object($report->getAdapter())->isIdenticalTo($adapter)
				->adapter($adapter)->call('extension_loaded')->withArguments('libxml')->once()
		;

		$adapter->extension_loaded = function($extension) { return false; };

		$this->assert
			->exception(function() use ($adapter) {
							$report = new reports\xunit($adapter);
						}
					)
			->isInstanceOf('mageekguy\atoum\exceptions\runtime')
			->hasMessage('libxml PHP extension is mandatory for xunit report')
			;
	}

	public function testSetAdapter()
	{
		$report = new reports\xunit();

		$this->assert
			->object($report->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($report)
			->object($report->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testHandleEvent()
	{
		$this->assert
			->if($report = new reports\xunit())
			->then
				->variable($report->getTitle())->isNull()
				->castToString($report)->isEmpty()
				->string($report->handleEvent(atoum\runner::runStop, new atoum\runner())->getTitle())->isEqualTo(atoum\reports\asynchronous\xunit::defaultTitle)
				->castToString($report)->isNotEmpty()
		;

		$report = new reports\xunit();

		$this->assert
			->string($report->setTitle($title = uniqid())->handleEvent(atoum\runner::runStop, new atoum\runner())->getTitle())
			->isEqualTo($title);

		$report = new reports\xunit();
		$this->mock('\mageekguy\atoum\writers\file');

		$writer = new \mock\mageekguy\atoum\writers\file();
		$writer->getMockController()->write = function($something) use ($writer) { return $writer; };

		$this->assert
			->when(function() use ($report, $writer) { $report->addWriter($writer)->handleEvent(atoum\runner::runStop, new \mageekguy\atoum\runner()); })
				->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
		;
	}
}

?>
