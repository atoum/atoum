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
		$this->assert->testedClass->isSubClassOf('mageekguy\atoum\report\fields\test\memory\cli');
	}

	public function test__construct()
	{
		$this->assert
			->if($field = new test\memory\phing())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMemoryColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getValue())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\test::runStop))
			->if($field = new test\memory\phing(null, null, null, null))
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMemoryColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getValue())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\test::runStop))
			->if($field = new test\memory\phing($prompt = new prompt(), $titleColorizer = new colorizer(), $memoryColorizer = new colorizer(), $locale = new locale()))
			->then
				->object($field->getPrompt())->isIdenticalTo($prompt)
				->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
				->object($field->getMemoryColorizer())->isIdenticalTo($memoryColorizer)
				->object($field->getLocale())->isIdenticalTo($locale)
				->variable($field->getValue())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\test::runStop))
		;
	}

	public function testSetPrompt()
	{
		$this->assert
			->if($field = new test\memory\phing())
			->then
				->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
			->if($field = new test\memory\phing(new prompt()))
			->then
				->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$this->assert
			->if($field = new test\memory\phing())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
			->if($field = new test\memory\phing(null, new colorizer()))
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetDurationColorizer()
	{
		$this->assert
			->if($field = new test\memory\phing())
			->then
				->object($field->setMemoryColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMemoryColorizer())->isIdenticalTo($colorizer)
			->if($field = new test\memory\phing(null, null, new colorizer()))
			->then
				->object($field->setMemoryColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMemoryColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testHandleEvent()
	{
		$this
			->assert
				->if($field = new test\memory\phing())
				->and($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getTotalMemoryUsage = $totalMemoryUsage = rand(0, PHP_INT_MAX))
				->and($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = true)
				->and($testController = new atoum\mock\controller())
				->and($testController->getTestedClassName = uniqid())
				->and($test = new \mock\mageekguy\atoum\test(null, null, $adapter, null, null, $testController))
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
			->assert
				->if($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getTotalMemoryUsage = $totalMemoryUsage = rand(0, PHP_INT_MAX))
				->and($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = true)
				->and($testController = new atoum\mock\controller())
				->and($testController->getTestedClassName = uniqid())
				->and($test = new \mock\mageekguy\atoum\test(null, null, $adapter, null, null, $testController))
				->and($test->getMockController()->getScore = $score)
				->and($defaultField = new test\memory\phing())
				->and($customField = new test\memory\phing($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $memoryColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale()))
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
