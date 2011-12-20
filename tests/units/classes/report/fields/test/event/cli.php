<?php

namespace mageekguy\atoum\tests\units\report\fields\test\event;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\report\fields\test
;

require_once __DIR__ . '/../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubClassOf('mageekguy\atoum\report\fields\test\event')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($field = new test\event\cli())
			->then
				->variable($field->getEvent())->isNull()
		;
	}

	public function testGetProgressBar()
	{

		$this
			->mock('mageekguy\atoum\test')
			->assert
				->if($field = new test\event\cli())
				->and($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = true)
				->and($testController = new mock\controller())
				->and($testController->getTestedClassName = uniqid())
				->and($test = new \mock\mageekguy\atoum\test(null, null, $adapter, null, null, $testController))
				->then
					->variable($field->getTest())->isNull()
					->exception(function() use ($field) { $field->getProgressBar(); })
						->isInstanceOf('logicException')
						->hasMessage('Unable to get progress bar because test is undefined')
				->if($field->handleEvent(atoum\test::runStart, $test))
				->then
					->object($field->getProgressBar())->isInstanceOf('mageekguy\atoum\test\cli\progressBar')
		;
	}

	public function testHandleEvent()
	{
		$this
			->mock('mageekguy\atoum\test')
			->assert
				->if($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = true)
				->and($testController = new mock\controller())
				->and($testController->getTestedClassName = uniqid())
				->and($test = new \mock\mageekguy\atoum\test(null, null, $adapter, null, null, $testController))
				->and($field = new test\event\cli())
				->then
					->boolean($field->handleEvent(atoum\runner::runStart, $test))->isFalse()
					->variable($field->getEvent())->isNull()
					->boolean($field->handleEvent(atoum\runner::runStop, $test))->isFalse()
					->variable($field->getEvent())->isNull()
					->boolean($field->handleEvent(atoum\test::runStart, $test))->isTrue()
					->string($field->getEvent())->isEqualTo(atoum\test::runStart)
					->boolean($field->handleEvent(atoum\test::beforeSetUp, $test))->isFalse()
					->variable($field->getEvent())->isNull()
					->boolean($field->handleEvent(atoum\test::afterSetUp, $test))->isFalse()
					->variable($field->getEvent())->isNull()
					->boolean($field->handleEvent(atoum\test::beforeTestMethod, $test))->isFalse()
					->variable($field->getEvent())->isNull()
					->boolean($field->handleEvent(atoum\test::fail, $test))->isTrue()
					->string($field->getEvent())->isEqualTo(atoum\test::fail)
					->boolean($field->handleEvent(atoum\test::error, $test))->isTrue()
					->string($field->getEvent())->isEqualTo(atoum\test::error)
					->boolean($field->handleEvent(atoum\test::exception, $test))->isTrue()
					->string($field->getEvent())->isEqualTo(atoum\test::exception)
					->boolean($field->handleEvent(atoum\test::success, $test))->isTrue()
					->string($field->getEvent())->isEqualTo(atoum\test::success)
					->boolean($field->handleEvent(atoum\test::afterTestMethod, $test))->isFalse()
					->variable($field->getEvent())->isNull()
					->boolean($field->handleEvent(atoum\test::beforeTearDown, $test))->isFalse()
					->variable($field->getEvent())->isNull()
					->boolean($field->handleEvent(atoum\test::afterTearDown, $test))->isFalse()
					->variable($field->getEvent())->isNull()
					->boolean($field->handleEvent(atoum\test::runStop, $test))->isTrue()
					->string($field->getEvent())->isEqualTo(atoum\test::runStop)
		;
	}

	public function testSetProgressBarInjector()
	{

		$this
			->mock('mageekguy\atoum\test')
			->assert
				->if($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = true)
				->and($testController = new mock\controller())
				->and($testController->getTestedClassName = uniqid())
				->and($test = new \mock\mageekguy\atoum\test(null, null, $adapter, null, null, $testController))
				->and($field = new test\event\cli())
				->then
					->object($field->setProgressBarInjector(function($test) use (& $progressBar) { return $progressBar = new atoum\test\cli\progressBar($test); }))->isIdenticalTo($field)
				->exception(function() use ($field) {
							$field->setProgressBarInjector(function() {});
						}
					)
					->isInstanceOf('invalidArgumentException')
					->hasMessage('Progress bar injector must take one argument')
		;
	}

	public function test__toString()
	{
		$this
			->mock('mageekguy\atoum\test')
			->assert
				->if($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = true)
				->and($testController = new atoum\mock\controller())
				->and($testController->getTestedClassName = uniqid())
				->and($test = new \mock\mageekguy\atoum\test(null, null, $adapter, null, null, $testController))
				->and($field = new test\event\cli())
				->and($count = rand(1, PHP_INT_MAX))
				->and($test->getMockController()->count = function() use ($count) { return $count; })
				->and($progressBar = new atoum\test\cli\progressBar($test))
				->then
					->castToString($field)->isEmpty()
				->if($field->handleEvent(atoum\test::runStart, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar)
				->if($field->handleEvent(atoum\test::beforeSetUp, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar)
				->if($field->handleEvent(atoum\test::afterSetUp, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar)
				->if($field->handleEvent(atoum\test::beforeTestMethod, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar)
				->if($field->handleEvent(atoum\test::fail, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar->refresh('F'))
				->if($field->handleEvent(atoum\test::error, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar->refresh('e'))
				->if($field->handleEvent(atoum\test::exception, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar->refresh('E'))
				->if($field->handleEvent(atoum\test::success, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar->refresh('S'))
				->if($field->handleEvent(atoum\test::uncompleted, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar->refresh('U'))
				->if($field->handleEvent(atoum\test::afterTestMethod, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar)
				->if($field->handleEvent(atoum\test::beforeTearDown, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar)
				->if($field->handleEvent(atoum\test::afterTearDown, $test))
				->then
					->castToString($field)->isEqualTo((string) $progressBar)
				->if($field->handleEvent(atoum\test::runStop, $test))
				->then
					->castToString($field)->isEqualTo(PHP_EOL)
		;
	}
}

?>
