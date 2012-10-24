<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\duration;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\tests\units,
	mageekguy\atoum\report\fields\runner\tests
;

require_once __DIR__ . '/../../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\tests\duration');
	}

	public function test__construct()
	{
		$this
			->if($field = new tests\duration\cli())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getDurationColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getValue())->isNull()
				->variable($field->getTestNumber())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetPrompt()
	{
		$this
			->if($field = new tests\duration\cli())
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
			->if($field = new tests\duration\cli())
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
			->if($field = new tests\duration\cli())
			->then
				->object($field->setDurationColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getDurationColorizer())->isIdenticalTo($colorizer)
				->object($field->setDurationColorizer())->isIdenticalTo($field)
				->object($field->getDurationColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetLocale()
	{
		$this
			->if($field = new tests\duration\cli())
			->then
				->object($field->setLocale($locale = new atoum\locale()))->isIdenticalTo($field)
				->object($field->getLocale())->isIdenticalTo($locale)
				->object($field->setLocale())->isIdenticalTo($field)
				->object($field->getLocale())
					->isNotIdenticalTo($locale)
					->isEqualTo(new atoum\locale())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new tests\duration\cli())
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, new atoum\runner()))->isFalse()
				->variable($field->getValue())->isNull()
				->variable($field->getTestNumber())->isNull()
			->if($score = new \mock\mageekguy\atoum\runner\score())
			->and($score->getMockController()->getTotalDuration = $totalDuration = (float) rand(1, PHP_INT_MAX))
			->and($runner = new \mock\mageekguy\atoum\runner())
			->and($runner->setScore($score))
			->and($runner->getMockController()->getTestNumber = $testsNumber = rand(1, PHP_INT_MAX))
			->then
				->boolean($field->handleEvent(atoum\runner::runStop, $runner))->isTrue()
				->float($field->getValue())->isEqualTo($totalDuration)
				->integer($field->getTestNumber())->isEqualTo($testsNumber)
		;
	}

	public function test__toString()
	{
		$this
			->if($score = new \mock\mageekguy\atoum\runner\score())
			->and($score->getMockController()->getTotalDuration = $totalDuration = (rand(1, 100) / 1000))
			->and($runner = new \mock\mageekguy\atoum\runner())
			->and($runner->setScore($score))
			->and($runner->getMockController()->getTestNumber = $testNumber = 1)
			->and($defaultField = new tests\duration\cli())
			->and($customField = new tests\duration\cli())
			->and($customField->setPrompt($prompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setDurationColorizer($durationColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new locale()))
			->then
				->castToString($defaultField)->isEqualTo($defaultField->getPrompt() . $defaultField->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
				->castToString($customField)->isEqualTo($prompt . sprintf('%s: %s.', $titleColorizer->colorize($locale->_('Total test duration')), $durationColorizer->colorize($locale->_('unknown'))) . PHP_EOL)
			->if($defaultField->handleEvent(atoum\runner::runStart, new atoum\runner()))
			->and($customField->handleEvent(atoum\runner::runStart, new atoum\runner()))
			->then
				->castToString($defaultField)->isEqualTo($defaultField->getPrompt() . $defaultField->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
				->castToString($customField)->isEqualTo($prompt . sprintf('%s: %s.', $titleColorizer->colorize($locale->_('Total test duration')), $durationColorizer->colorize($locale->_('unknown'))) . PHP_EOL)
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->and($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(
						$defaultField->getPrompt() . sprintf($defaultField->getLocale()->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($defaultField->getLocale()->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL
					)
				->castToString($customField)->isEqualTo($prompt .
						sprintf(
							'%s: %s.',
							$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', $testNumber)),
							$durationColorizer->colorize(sprintf($locale->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration))
						) .
						PHP_EOL
					)
			->if($runner->getMockController()->getTestNumber = $testNumber = rand(2, PHP_INT_MAX))
			->and($defaultField = new tests\duration\cli())
			->and($customField = new tests\duration\cli())
			->and($customField->setPrompt($prompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setDurationColorizer($durationColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new locale()))
			->then
				->castToString($defaultField)->isEqualTo($defaultField->getPrompt() . $defaultField->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
				->castToString($customField)->isEqualTo($prompt . sprintf('%s: %s.', $titleColorizer->colorize($locale->_('Total test duration')), $durationColorizer->colorize($locale->_('unknown'))) . PHP_EOL)
			->if($defaultField->handleEvent(atoum\runner::runStart, new atoum\runner()))
			->and($customField->handleEvent(atoum\runner::runStart, new atoum\runner()))
			->then
				->castToString($defaultField)->isEqualTo($defaultField->getPrompt() . $defaultField->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
				->castToString($customField)->isEqualTo($prompt . sprintf('%s: %s.', $titleColorizer->colorize($locale->_('Total test duration')), $durationColorizer->colorize($locale->_('unknown'))) . PHP_EOL)
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->and($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(
						$defaultField->getPrompt() . sprintf($defaultField->getLocale()->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($defaultField->getLocale()->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL
					)
				->castToString($customField)->isEqualTo($prompt .
						sprintf(
							'%s: %s.',
							$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', $testNumber)),
							$durationColorizer->colorize(sprintf($locale->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration))
						) .
						PHP_EOL
					)
		;
	}
}
