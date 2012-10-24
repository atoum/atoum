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

class phing extends atoum\test
{
	public function testClass()
	{
	  $this->testedClass->extends('mageekguy\atoum\report\fields\runner\tests\memory\cli');
	}

	public function test__construct()
	{
		$this
			->if($field = new memory\phing())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMemoryColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getValue())->isNull()
				->variable($field->getTestNumber())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetPrompt()
	{
		$this
			->if($field = new memory\phing())
			->then
				->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
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
			->if($field = new memory\phing())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
				->object($field->setTitleColorizer())->isIdenticalTo($field)
				->object($field->getTitleColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetMemoryColorizer()
	{
		$this
			->if($field = new memory\phing())
			->then
				->object($field->setMemoryColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMemoryColorizer())->isIdenticalTo($colorizer)
				->object($field->setMemoryColorizer())->isIdenticalTo($field)
				->object($field->getMemoryColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetLocale()
	{
		$this
			->if($field = new memory\phing())
			->then
				->object($field->setLocale($locale = new atoum\locale()))->isIdenticalTo($field)
				->object($field->getLocale())->isIdenticalTo($locale)
			->if($field = new memory\phing(null, null, null, $locale = new atoum\locale()))
			->then
				->object($field->setLocale($locale = new atoum\locale()))->isIdenticalTo($field)
				->object($field->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testHandleEvent()
	{

		$this
			->if($field = new memory\phing())
			->and($score = new \mock\mageekguy\atoum\runner\score())
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
			->if($score = new \mock\mageekguy\atoum\runner\score())
			->and($score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(1, PHP_INT_MAX); })
			->and($runner = new \mock\mageekguy\atoum\runner())
			->and($runner->setScore($score))
			->and($runner->getMockController()->getTestNumber = $testNumber = rand(1, PHP_INT_MAX))
			->and($defaultField = new memory\phing())
			->then
				->castToString($defaultField)->isEqualTo(
						$defaultField->getPrompt() . $defaultField->getTitleColorizer()->colorize($defaultField->getLocale()->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $defaultField->getMemoryColorizer()->colorize($defaultField->getLocale()->_('unknown')) . '.'
					)
			->if($customField = new memory\phing())
			->and($customField->setPrompt($prompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMemoryColorizer($memoryColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new locale()))
			->then
				->castToString($customField)->isEqualTo(
						$prompt . $titleColorizer->colorize($locale->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $memoryColorizer->colorize($locale->_('unknown')) . '.'
					)
			->if($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEqualTo(
						$defaultField->getPrompt() . $defaultField->getTitleColorizer()->colorize($defaultField->getLocale()->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $defaultField->getMemoryColorizer()->colorize($defaultField->getLocale()->_('unknown')) . '.'
					)
			->if($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEqualTo(
						$prompt . $titleColorizer->colorize($locale->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $memoryColorizer->colorize($locale->_('unknown')) . '.'
					)
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo($defaultField->getPrompt() . $defaultField->getTitleColorizer()->colorize($defaultField->getLocale()->__('Total test memory usage', 'Total tests memory usage', $testNumber)) . ': ' . $defaultField->getMemoryColorizer()->colorize(sprintf($defaultField->getLocale()->_('%4.2f Mb'), $totalMemoryUsage / 1048576)) . '.')
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo($prompt . $titleColorizer->colorize($locale->__('Total test memory usage', 'Total tests memory usage', $testNumber)) . ': ' . $memoryColorizer->colorize(sprintf($locale->_('%4.2f Mb'), $totalMemoryUsage / 1048576)) . '.')
		;
	}
}
