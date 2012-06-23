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
		$this->assert
			->testedClass->isSubClassOf('mageekguy\atoum\report\fields\test\run')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($field = new test\run\cli())
			->then
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getTestClass())->isNull()
				->object($field->getLocale())->isEqualTo(new atoum\locale())
			->if($field = new test\run\cli($prompt = new prompt(), $colorizer = new colorizer(), $locale = new locale()))
			->then
				->object($field->getPrompt())->isIdenticalTo($prompt)
				->object($field->getColorizer())->isIdenticalTo($colorizer)
				->object($field->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetPrompt()
	{
		$this->assert
			->if($field = new test\run\cli())
			->then
				->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
			->if($field = new test\run\cli(new prompt()))
			->then
				->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetColorizer()
	{
		$this->assert
			->if($field = new test\run\cli())
			->then
				->object($field->setColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getColorizer())->isIdenticalTo($colorizer)
			->if($field = new test\run\cli(null, new colorizer()))
			->then
				->object($field->setColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testHandleEvent()
	{

		$this
			->assert
				->if($field = new test\run\cli())
				->and($adapter = new adapter())
				->and($adapter->class_exists = true)
				->and($testController = new mock\controller())
				->and($testController->getTestedClassName = uniqid())
				->and($test = new \mock\mageekguy\atoum\test(null, null, $adapter, null, null, $testController))
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
			->assert
				->if($adapter = new adapter())
				->and($adapter->class_exists = true)
				->and($testController = new mock\controller())
				->and($testController->getTestedClassName = uniqid())
				->and($test = new \mock\mageekguy\atoum\test(null, null, $adapter, null, null, $testController))
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
				->if($customField = new test\run\cli($prompt = new prompt(uniqid()), $colorizer = new colorizer(uniqid(), uniqid()), $locale = new locale()))
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
