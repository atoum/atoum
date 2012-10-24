<?php

namespace mageekguy\atoum\tests\units\report\fields\test\duration;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\tests\units
;

require_once __DIR__ . '/../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\test\duration');
	}

	public function test__construct()
	{
		$this
			->if($field = new test\duration\cli())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getDurationColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getValue())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\test::runStop))
		;
	}

	public function testSetPrompt()
	{
		$this
			->if($field = new test\duration\cli())
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
			->if($field = new test\duration\cli())
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
			->if($field = new test\duration\cli())
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
			->if($field = new test\duration\cli())
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
			->if($field = new test\duration\cli())
			->and($score = new \mock\mageekguy\atoum\score())
			->and($score->getMockController()->getTotalDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); })
			->and($adapter = new adapter())
			->and($adapter->class_exists = true)
			->and($testController = new mock\controller())
			->and($testController->getTestedClassName = uniqid())
			->and($testController->getScore = $score)
			->and($test = new \mock\mageekguy\atoum\test($adapter))
			->then
				->boolean($field->handleEvent(atoum\runner::runStop, $test))->isFalse()
				->variable($field->getValue())->isNull()
				->boolean($field->handleEvent(atoum\test::runStop, $test))->isTrue()
				->integer($field->getValue())->isEqualTo($runningDuration)
		;
	}

	public function test__toString()
	{
		$this
			->if($adapter = new adapter())
			->and($adapter->class_exists = true)
			->and($score = new \mock\mageekguy\atoum\score())
			->and($score->getMockController()->getTotalDuration = $runningDuration = rand(1, 1000) / 1000)
			->and($testController = new mock\controller())
			->and($testController->getTestedClassName = uniqid())
			->and($testController->getScore = $score)
			->and($test = new \mock\mageekguy\atoum\test($adapter))
			->and($defaultField = new test\duration\cli())
			->and($customField = new test\duration\cli())
			->and($customField->setPrompt($prompt = new prompt()))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer()))
			->and($customField->setDurationColorizer($durationColorizer = new colorizer()))
			->and($customField->setLocale($locale = new locale()))
			->then
				->castToString($defaultField)->isEqualTo('Test duration: unknown.' . PHP_EOL)
				->castToString($customField)->isEqualTo(
						$prompt .
						sprintf(
							'%1$s: %2$s.',
							$titleColorizer->colorize($locale->_('Test duration')),
							$locale->_('unknown')
						) .
						PHP_EOL
					)
			->if($defaultField->handleEvent(atoum\runner::runStop, $test))
			->then
				->castToString($defaultField)->isEqualTo('Test duration: unknown.' . PHP_EOL)
			->if($customField->handleEvent(atoum\runner::runStop, $test))
			->then
				->castToString($customField)->isEqualTo(
						$prompt .
						sprintf(
							'%1$s: %2$s.',
							$titleColorizer->colorize($locale->_('Test duration')),
							$locale->_('unknown')
						) .
						PHP_EOL
					)
			->if($defaultField->handleEvent(atoum\test::runStop, $test))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('Test duration: %4.2f second.', $runningDuration) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::runStop, $test))
			->then
				->castToString($customField)->isEqualTo(
						$prompt .
						sprintf(
							'%1$s: %2$s.',
							$titleColorizer->colorize($locale->_('Test duration')),
							$durationColorizer->colorize(sprintf($locale->__('%4.2f second', '%4.2f seconds', $runningDuration), $runningDuration))
						) .
						PHP_EOL
					)
			->if($score->getMockController()->getTotalDuration = $runningDuration = rand(2, PHP_INT_MAX))
			->and($defaultField = new test\duration\cli())
			->and($customField = new test\duration\cli())
			->and($customField->setPrompt($prompt = new prompt()))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer()))
			->and($customField->setDurationColorizer($durationColorizer = new colorizer()))
			->and($customField->setLocale($locale = new locale()))
			->then
				->castToString($defaultField)->isEqualTo('Test duration: unknown.' . PHP_EOL)
				->castToString($customField)->isEqualTo(
						$prompt .
						sprintf(
							'%1$s: %2$s.',
							$titleColorizer->colorize($locale->_('Test duration')),
							$locale->_('unknown')
						) .
						PHP_EOL
					)
			->if($defaultField->handleEvent(atoum\runner::runStop, $test))
			->then
				->castToString($defaultField)->isEqualTo('Test duration: unknown.' . PHP_EOL)
			->if($customField->handleEvent(atoum\runner::runStop, $test))
			->then
				->castToString($customField)->isEqualTo(
						$prompt .
						sprintf(
							'%1$s: %2$s.',
							$titleColorizer->colorize($locale->_('Test duration')),
							$locale->_('unknown')
						) .
						PHP_EOL
					)
			->if($defaultField->handleEvent(atoum\test::runStop, $test))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('Test duration: %4.2f seconds.', $runningDuration) . PHP_EOL)
			->if($customField->handleEvent(atoum\test::runStop, $test))
			->then
				->castToString($customField)->isEqualTo(
						$prompt .
						sprintf(
							'%1$s: %2$s.',
							$titleColorizer->colorize($locale->_('Test duration')),
							$durationColorizer->colorize(sprintf($locale->__('%4.2f second', '%4.2f seconds', $runningDuration), $runningDuration))
						) .
						PHP_EOL
					)
		;
	}
}
