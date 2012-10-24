<?php

namespace mageekguy\atoum\tests\units\report\fields\test\memory;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\tests\units
;

require_once __DIR__ . '/../../../../../runner.php';

class phing extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\test\memory\cli');
	}

	public function test__construct()
	{
		$this
			->if($field = new test\memory\phing())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMemoryColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getValue())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\test::runStop))
		;
	}

	public function testSetPrompt()
	{
		$this
			->if($field = new test\memory\phing())
			->then
				->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
				->object($field->setPrompt())->isIdenticalTo($field)
				->object($field->getPrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetTitleColorizer()
	{
		$this
			->if($field = new test\memory\phing())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
				->object($field->setTitleColorizer())->isIdenticalTo($field)
				->object($field->getTitleColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetDurationColorizer()
	{
		$this
			->if($field = new test\memory\phing())
			->then
				->object($field->setMemoryColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMemoryColorizer())->isIdenticalTo($colorizer)
				->object($field->setMemoryColorizer())->isIdenticalTo($field)
				->object($field->getMemoryColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new test\memory\phing())
			->and($score = new \mock\mageekguy\atoum\score())
			->and($score->getMockController()->getTotalMemoryUsage = $totalMemoryUsage = rand(0, PHP_INT_MAX))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = true)
			->and($testController = new atoum\mock\controller())
			->and($testController->getTestedClassName = uniqid())
			->and($test = new \mock\mageekguy\atoum\test($adapter))
			->and($test->getMockController()->getScore = $score)
			->then
				->boolean($field->handleEvent(atoum\test::runStart, $test))->isFalse()
				->variable($field->getValue())->isNull()
				->boolean($field->handleEvent(atoum\test::runStop, $test))->isTrue()
				->integer($field->getValue())->isEqualTo($totalMemoryUsage)
		;
	}

	public function test__toString()
	{
		$this
			->if($score = new \mock\mageekguy\atoum\score())
			->and($score->getMockController()->getTotalMemoryUsage = $totalMemoryUsage = rand(0, PHP_INT_MAX))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = true)
			->and($testController = new atoum\mock\controller())
			->and($testController->getTestedClassName = uniqid())
			->and($test = new \mock\mageekguy\atoum\test($adapter))
			->and($test->getMockController()->getScore = $score)
			->and($defaultField = new test\memory\phing())
			->and($customField = new test\memory\phing())
			->and($customField->setPrompt($prompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMemoryColorizer($memoryColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new locale()))
			->then
				->castToString($defaultField)->isEqualTo($defaultField->getPrompt() . $defaultField->getLocale()->_('unknown'))
				->castToString($customField)->isEqualTo(
						$prompt .
						sprintf(
							$locale->_('%s'),
							$memoryColorizer->colorize($locale->_('unknown'))
						)
					)
			->if($defaultField->handleEvent(atoum\test::runStart, $test))
			->then
				->castToString($defaultField)->isEqualTo($defaultField->getPrompt() . $defaultField->getLocale()->_('unknown'))
			->if($customField->handleEvent(atoum\test::runStart, $test))
			->then
				->castToString($customField)->isEqualTo(
						$prompt .
						sprintf(
							$locale->_('%s'),
							$memoryColorizer->colorize($locale->_('unknown'))
						)
					)
			->if($defaultField->handleEvent(atoum\test::runStop, $test))
			->then
				->castToString($defaultField)->isEqualTo($defaultField->getPrompt() . sprintf($defaultField->getLocale()->_('%4.2f Mb'), $totalMemoryUsage / 1048576))
			->if($customField->handleEvent(atoum\test::runStop, $test))
			->then
				->castToString($customField)->isEqualTo(
						$prompt .
						sprintf(
							$locale->_('%s'),
							$memoryColorizer->colorize(sprintf($locale->_('%4.2f Mb'), $totalMemoryUsage / 1048576))
						)
					)
		;
	}
}
