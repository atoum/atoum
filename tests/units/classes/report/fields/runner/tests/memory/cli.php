<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\memory;

use
	mageekguy\atoum,
	mageekguy\atoum\runner,
	mageekguy\atoum\locale,
	mageekguy\atoum\tests\units,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner\tests\memory
;

require_once __DIR__ . '/../../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubClassOf('mageekguy\atoum\report\fields\runner\tests\memory')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($field = new memory\cli())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMemoryColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getValue())->isNull()
				->variable($field->getTestNumber())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
			->if($field = new memory\cli(null, null, null, null))
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMemoryColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getValue())->isNull()
				->variable($field->getTestNumber())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
			->if($field = new memory\cli($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(), $memoryColorizer = new colorizer(), $locale = new locale()))
			->then
				->object($field->getLocale())->isIdenticalTo($locale)
				->object($field->getPrompt())->isIdenticalTo($prompt)
				->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
				->object($field->getMemoryColorizer())->isIdenticalTo($memoryColorizer)
				->variable($field->getValue())->isNull()
				->variable($field->getTestNumber())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetPrompt()
	{
		$this->assert
			->if($field = new memory\cli())
			->then
				->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
			->if($field = new memory\cli(new prompt()))
			->then
				->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$this->assert
			->if($field = new memory\cli())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
			->if($field = new memory\cli(null, new colorizer()))
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetMemoryColorizer()
	{
		$this->assert
			->if($field = new memory\cli())
			->then
				->object($field->setMemoryColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMemoryColorizer())->isIdenticalTo($colorizer)
			->if($field = new memory\cli(null, null, new colorizer()))
			->then
				->object($field->setMemoryColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMemoryColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetLocale()
	{
		$this->assert
			->if($field = new memory\cli())
			->then
				->object($field->setLocale($locale = new atoum\locale()))->isIdenticalTo($field)
				->object($field->getLocale())->isIdenticalTo($locale)
			->if($field = new memory\cli(null, null, null, $locale = new atoum\locale()))
			->then
				->object($field->setLocale($locale = new atoum\locale()))->isIdenticalTo($field)
				->object($field->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testHandleEvent()
	{

		$this
			->assert
				->if($field = new memory\cli())
				->and($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(1, PHP_INT_MAX); })
				->and($runner = new \mock\mageekguy\atoum\runner())
				->and($runner->setScore($score))
				->and($runner->getMockController()->getTestNumber = function () use (& $testNumber) { return $testNumber = rand(0, PHP_INT_MAX); })
				->then
					->boolean($field->handleEvent(atoum\runner::runStart, new atoum\runner()))->isFalse()
					->variable($field->getValue())->isNull()
					->variable($field->getTestNumber())->isNull()
					->boolean($field->handleEvent(atoum\runner::runStop, $runner))->isTrue()
					->integer($field->getValue())->isEqualTo($totalMemoryUsage)
					->integer($field->getTestNumber())->isEqualTo($testNumber)
		;
	}

	public function test__toString()
	{
		$this
			->assert
				->if($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(1, PHP_INT_MAX); })
				->and($runner = new \mock\mageekguy\atoum\runner())
				->and($runner->setScore($score))
				->and($runner->getMockController()->getTestNumber = $testNumber = rand(1, PHP_INT_MAX))
				->and($defaultField = new memory\cli())
				->then
					->castToString($defaultField)->isEqualTo(
							$defaultField->getPrompt() . $defaultField->getTitleColorizer()->colorize($defaultField->getLocale()->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $defaultField->getMemoryColorizer()->colorize($defaultField->getLocale()->_('unknown')) . '.' . PHP_EOL
						)
				->if($customField = new memory\cli($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $memoryColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale()))
				->then
					->castToString($customField)->isEqualTo(
							$prompt . $titleColorizer->colorize($locale->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $memoryColorizer->colorize($locale->_('unknown')) . '.' . PHP_EOL
						)
				->if($defaultField->handleEvent(atoum\runner::runStart, $runner))
				->then
					->castToString($defaultField)->isEqualTo(
							$defaultField->getPrompt() . $defaultField->getTitleColorizer()->colorize($defaultField->getLocale()->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $defaultField->getMemoryColorizer()->colorize($defaultField->getLocale()->_('unknown')) . '.' . PHP_EOL
						)
				->if($customField->handleEvent(atoum\runner::runStart, $runner))
				->then
					->castToString($customField)->isEqualTo(
							$prompt . $titleColorizer->colorize($locale->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $memoryColorizer->colorize($locale->_('unknown')) . '.' . PHP_EOL
						)
				->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
				->then
					->castToString($defaultField)->isEqualTo($defaultField->getPrompt() . $defaultField->getTitleColorizer()->colorize($defaultField->getLocale()->__('Total test memory usage', 'Total tests memory usage', $testNumber)) . ': ' . $defaultField->getMemoryColorizer()->colorize(sprintf($defaultField->getLocale()->_('%4.2f Mb'), $totalMemoryUsage / 1048576)) . '.' . PHP_EOL)
				->if($customField->handleEvent(atoum\runner::runStop, $runner))
				->then
					->castToString($customField)->isEqualTo($prompt . $titleColorizer->colorize($locale->__('Total test memory usage', 'Total tests memory usage', $testNumber)) . ': ' . $memoryColorizer->colorize(sprintf($locale->_('%4.2f Mb'), $totalMemoryUsage / 1048576)) . '.' . PHP_EOL)
		;
	}
}
