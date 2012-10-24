<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\event;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\report\fields\runner
;

require_once __DIR__ . '/../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\event');
	}

	public function test__construct()
	{
		$this
			->if($field = new runner\event\cli())
			->then
				->variable($field->getObservable())->isNull()
				->variable($field->getEvent())->isNull()
				->object($field->getProgressBar())->isEqualTo(new atoum\cli\progressBar())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($testController = new atoum\mock\controller())
			->and($testController->__construct = function() {})
			->and($test = new \mock\mageekguy\atoum\test())
			->and($runner = new atoum\runner())
			->and($field = new runner\event\cli())
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, $runner))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\runner::runStart)
				->object($field->getObservable())->isIdenticalTo($runner)
				->boolean($field->handleEvent(atoum\test::runStart, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::beforeSetUp, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::afterSetUp, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::beforeTestMethod, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::fail, $test))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\test::fail)
				->object($field->getObservable())->isIdenticalTo($test)
				->boolean($field->handleEvent(atoum\test::error, $test))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\test::error)
				->object($field->getObservable())->isIdenticalTo($test)
				->boolean($field->handleEvent(atoum\test::exception, $test))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\test::exception)
				->object($field->getObservable())->isIdenticalTo($test)
				->boolean($field->handleEvent(atoum\test::success, $test))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\test::success)
				->object($field->getObservable())->isIdenticalTo($test)
				->boolean($field->handleEvent(atoum\test::afterTestMethod, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::beforeTearDown, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::afterTearDown, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::runStop, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\runner::runStop, $runner))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\runner::runStop)
				->object($field->getObservable())->isIdenticalTo($runner)
		;
	}

	public function test__toString()
	{
		$this
			->if($testMethodNumber = rand(5, PHP_INT_MAX))
			->and($runnerController = new atoum\mock\controller())
			->and($runnerController->__construct = function() {})
			->and($runnerController->getTestMethodNumber = function() use ($testMethodNumber) { return $testMethodNumber; })
			->and($runner = new \mock\mageekguy\atoum\runner())
			->and($field = new runner\event\cli())
			->and($progressBar = new atoum\cli\progressBar($runner->getTestMethodNumber()))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($field)->isEqualTo((string) $progressBar)
			->if($field->handleEvent(atoum\test::beforeSetUp, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar)
			->if($field->handleEvent(atoum\test::afterSetUp, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar)
			->if($field->handleEvent(atoum\test::beforeTestMethod, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar)
			->if($field->handleEvent(atoum\test::fail, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar->refresh('F'))
			->if($field->handleEvent(atoum\test::error, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar->refresh('E'))
			->if($field->handleEvent(atoum\test::exception, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar->refresh('X'))
			->if($field->handleEvent(atoum\test::success, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar->refresh('S'))
			->if($field->handleEvent(atoum\test::uncompleted, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar->refresh('U'))
			->if($field->handleEvent(atoum\test::void, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar->refresh('0'))
			->if($field->handleEvent(atoum\test::skipped, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar->refresh('-'))
			->if($field->handleEvent(atoum\test::afterTestMethod, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar)
			->if($field->handleEvent(atoum\test::beforeTearDown, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar)
			->if($field->handleEvent(atoum\test::afterTearDown, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar)
			->if($field->handleEvent(atoum\test::runStop, $this))
			->then
				->castToString($field)->isEqualTo((string) $progressBar)
			->if($field->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($field)->isEqualTo(PHP_EOL)
		;
	}
}
