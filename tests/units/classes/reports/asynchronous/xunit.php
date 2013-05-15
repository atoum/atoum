<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\runner,
	mageekguy\atoum\report,
	mageekguy\atoum\reports\asynchronous as reports
;

require_once __DIR__ . '/../../../runner.php';

class xunit extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\reports\asynchronous');
	}

	public function testClassConstants()
	{
		$this->string(atoum\reports\asynchronous\xunit::defaultTitle)->isEqualTo('atoum testsuite');
	}

	public function test__construct()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($report = new reports\xunit($adapter))
			->then
				->array($report->getFields(atoum\runner::runStart))->isEmpty()
				->object($report->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->if($adapter->extension_loaded = false)
			->then
				->exception(function() use ($adapter) { new reports\xunit($adapter); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('libxml PHP extension is mandatory for xunit report')
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($report = new reports\xunit($adapter))
			->then
				->variable($report->getTitle())->isNull()
				->castToString($report)->isEmpty()
				->string($report->handleEvent(atoum\runner::runStop, new atoum\runner())->getTitle())->isEqualTo(atoum\reports\asynchronous\xunit::defaultTitle)
				->castToString($report)->isNotEmpty()
			->if($report = new reports\xunit($adapter))
			->then
				->string($report->setTitle($title = uniqid())->handleEvent(atoum\runner::runStop, new atoum\runner())->getTitle())->isEqualTo($title)
			->if($report = new reports\xunit($adapter))
			->and($writer = new \mock\mageekguy\atoum\writers\file())
			->and($writer->getMockController()->write = $writer)
			->then
				->when(function() use ($report, $writer) { $report->addWriter($writer)->handleEvent(atoum\runner::runStop, new \mageekguy\atoum\runner()); })
					->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
		;
	}

	public function testBuild()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($runner = new atoum\runner())
			->and($score = new runner\score())
			->and($report = new reports\xunit($adapter))
			->and($runner->setScore($score))
			->and($path = join(
				DIRECTORY_SEPARATOR,
				array(
					__DIR__,
					'xunit',
					'resources'
				)
			))
			->and($score->addDuration($file = 'file', $class = 'class', $method = 'method', $duration = 1))
			->and($report->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($report)->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array($path, '1.xml')))
			->if($score->addFail($otherFile = 'otherFile', $otherClass = 'otherClass', $otherMethod = 'otherMethod', 1, $asserter = 'asserter', $reason = 'reason'))
			->and($report->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($report)->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array($path, '2.xml')))
			->if($exception = new \mock\Exception())
			->and($exception->getMockController()->__toString = $trace = 'trace')
			->and($score->addException($otherFile, $otherClass, $thirdMethod = 'thirdMethod', 1, $exception))
			->and($report->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($report)->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array($path, '3.xml')))
			->if($score->addError($thirdFile = 'thirdFile', $thirdClassFqn = 'package\\thirdClass', $fourthMethod = 'fourthMethod', rand(0, PHP_INT_MAX), $type = E_ERROR, $message = 'message'))
			->and($report->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($report)->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array($path, '4.xml')))
			->if($score->addUncompletedMethod($class, $otherMethod, $exitCode = 1, $output = 'output'))
			->and($report->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($report)->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array($path, '5.xml')))
			->if($score->addSkippedMethod($file, $class, $thirdMethod, $line = rand(1, PHP_INT_MAX), $message))
			->and($report->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($report)->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array($path, '6.xml')))
		;
	}
}
