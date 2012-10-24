<?php

namespace mageekguy\atoum\tests\units\report\fields\test\run;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\locale,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\test
;

require_once __DIR__ . '/../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\test\run');
	}

	public function test__construct()
	{
		$this
			->if($field = new test\run\cli())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getTestClass())->isNull()
		;
	}

	public function testSetPrompt()
	{
		$this
			->if($field = new test\run\cli())
			->then
				->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
				->object($field->setPrompt())->isIdenticalTo($field)
				->object($field->getPrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetColorizer()
	{
		$this
			->if($field = new test\run\cli())
			->then
				->object($field->setColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getColorizer())->isIdenticalTo($colorizer)
				->object($field->setColorizer())->isIdenticalTo($field)
				->object($field->getColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new test\run\cli())
			->and($adapter = new adapter())
			->and($adapter->class_exists = true)
			->and($testController = new mock\controller())
			->and($testController->getTestedClassName = uniqid())
			->and($test = new \mock\mageekguy\atoum\test($adapter))
			->then
				->boolean($field->handleEvent(atoum\test::runStop, $test))->isFalse()
				->variable($field->getTestClass())->isNull()
				->boolean($field->handleEvent(atoum\test::beforeSetUp, $test))->isFalse()
				->variable($field->getTestClass())->isNull()
				->boolean($field->handleEvent(atoum\test::afterSetUp, $test))->isFalse()
				->variable($field->getTestClass())->isNull()
				->boolean($field->handleEvent(atoum\test::beforeTestMethod, $test))->isFalse()
				->variable($field->getTestClass())->isNull()
				->boolean($field->handleEvent(atoum\test::fail, $test))->isFalse()
				->variable($field->getTestClass())->isNull()
				->boolean($field->handleEvent(atoum\test::error, $test))->isFalse()
				->variable($field->getTestClass())->isNull()
				->boolean($field->handleEvent(atoum\test::exception, $test))->isFalse()
				->variable($field->getTestClass())->isNull()
				->boolean($field->handleEvent(atoum\test::success, $test))->isFalse()
				->variable($field->getTestClass())->isNull()
				->boolean($field->handleEvent(atoum\test::afterTestMethod, $test))->isFalse()
				->variable($field->getTestClass())->isNull()
				->boolean($field->handleEvent(atoum\test::beforeTearDown, $test))->isFalse()
				->variable($field->getTestClass())->isNull()
				->boolean($field->handleEvent(atoum\test::afterTearDown, $test))->isFalse()
				->variable($field->getTestClass())->isNull()
				->boolean($field->handleEvent(atoum\test::runStart, $test))->isTrue()
				->string($field->getTestClass())->isEqualTo($test->getClass())
		;
	}

	public function test__toString()
	{
		$this
			->if($adapter = new adapter())
			->and($adapter->class_exists = true)
			->and($testController = new mock\controller())
			->and($testController->getTestedClassName = uniqid())
			->and($test = new \mock\mageekguy\atoum\test($adapter))
			->and($defaultField = new test\run\cli())
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::runStop, $test))
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::beforeSetUp, $test))
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::afterSetUp, $test))
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::beforeTestMethod, $test))
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::fail, $test))
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::error, $test))
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::exception, $test))
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::success, $test))
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::afterTestMethod, $test))
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::beforeTearDown, $test))
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::afterTearDown, $test))
			->then
				->castToString($defaultField)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->if($defaultField->handleEvent(atoum\test::runStart, $test))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('%s...', $test->getClass()) . PHP_EOL)
			->if($customField = new test\run\cli())
			->and($customField->setPrompt($prompt = new prompt(uniqid())))
			->and($customField->setColorizer($colorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new locale()))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::runStop, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::beforeSetUp, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::afterSetUp, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::beforeTestMethod, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::fail, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::error, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::exception, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::success, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::afterTestMethod, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::beforeTearDown, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::afterTearDown, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::runStart, $test))
			->then
				->castToString($customField)->isEqualTo($prompt . sprintf($locale->_('%s...'), $colorizer->colorize($test->getClass())) . PHP_EOL)
		;
	}
}
