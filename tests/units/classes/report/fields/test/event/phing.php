<?php

namespace mageekguy\atoum\tests\units\report\fields\test\event;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\report\fields\test
;

require_once __DIR__ . '/../../../../../runner.php';

class phing extends atoum\test
{
	public function testClass()
	{
	  $this->testedClass->extends('mageekguy\atoum\report\fields\test\event\cli');
	}

	public function test__construct()
	{
		$this
			->if($field = new test\event\phing())
			->then
				->variable($field->getObservable())->isNull()
				->variable($field->getEvent())->isNull()
				->object($field->getProgressBar())->isEqualTo(new atoum\cli\progressBar())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = true)
			->and($testController = new mock\controller())
			->and($testController->getTestedClassName = uniqid())
			->and($test = new \mock\mageekguy\atoum\test($adapter))
			->and($field = new test\event\phing())
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\runner::runStop, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::runStart, $test))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\test::runStart)
				->object($field->getObservable())->isIdenticalTo($test)
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
				->boolean($field->handleEvent(atoum\test::runStop, $test))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\test::runStop)
				->object($field->getObservable())->isIdenticalTo($test)
		;
	}

	public function test__toString()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = true)
			->and($testController = new atoum\mock\controller())
			->and($testController->getTestedClassName = uniqid())
			->and($test = new \mock\mageekguy\atoum\test($adapter))
			->and($field = new test\event\phing())
			->and($count = rand(1, PHP_INT_MAX))
			->and($test->getMockController()->count = function() use ($count) { return $count; })
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::runStart, $test))
			->then
				->castToString($field)->isEqualTo('[')
			->if($field->handleEvent(atoum\test::beforeSetUp, $test))
			->then
				->castToString($field)->isEqualTo('')
			->if($field->handleEvent(atoum\test::afterSetUp, $test))
			->then
				->castToString($field)->isEqualTo('')
			->if($field->handleEvent(atoum\test::beforeTestMethod, $test))
			->then
				->castToString($field)->isEqualTo('')
			->if($field->handleEvent(atoum\test::fail, $test))
			->then
				->castToString($field)->isEqualTo('F')
			->if($field->handleEvent(atoum\test::error, $test))
			->then
				->castToString($field)->isEqualTo('e')
			->if($field->handleEvent(atoum\test::exception, $test))
			->then
				->castToString($field)->isEqualTo('E')
			->if($field->handleEvent(atoum\test::success, $test))
			->then
				->castToString($field)->isEqualTo('S')
			->if($field->handleEvent(atoum\test::uncompleted, $test))
			->then
				->castToString($field)->isEqualTo('U')
			->if($field->handleEvent(atoum\test::afterTestMethod, $test))
			->then
				->castToString($field)->isEqualTo('')
			->if($field->handleEvent(atoum\test::beforeTearDown, $test))
			->then
				->castToString($field)->isEqualTo('')
			->if($field->handleEvent(atoum\test::afterTearDown, $test))
			->then
				->castToString($field)->isEqualTo('')
			->if($field->handleEvent(atoum\test::runStop, $test))
			->then
				->castToString($field)->isEqualTo('] ')
		;
	}
}
