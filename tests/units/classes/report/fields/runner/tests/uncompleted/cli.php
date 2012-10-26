<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\uncompleted;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\tests\units,
	mock\mageekguy\atoum as mock,
	mageekguy\atoum\report\fields\runner\tests
;

require_once __DIR__ . '/../../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\tests\uncompleted');
	}

	public function test__construct()
	{
		$this
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->getTitlePrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMethodPrompt())->isEqualTo(new prompt())
				->object($field->getMethodColorizer())->isEqualTo(new colorizer())
				->object($field->getOutputPrompt())->isEqualTo(new prompt())
				->object($field->getOutputColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getRunner())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetTitlePrompt()
	{
		$this
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setTitlePrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isIdenticalTo($prompt)
				->object($field->setTitlePrompt())->isIdenticalTo($field)
				->object($field->getTitlePrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetTitleColorizer()
	{
		$this
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
				->object($field->setTitleColorizer())->isIdenticalTo($field)
				->object($field->getTitleColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetMethodPrompt()
	{
		$this
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setMethodPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getMethodPrompt())->isIdenticalTo($prompt)
				->object($field->setMethodPrompt())->isIdenticalTo($field)
				->object($field->getMethodPrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetMethodColorizer()
	{
		$this
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
				->object($field->setMethodColorizer())->isIdenticalTo($field)
				->object($field->getMethodColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetOutputPrompt()
	{
		$this
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setOutputPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getOutputPrompt())->isIdenticalTo($prompt)
				->object($field->setOutputPrompt())->isIdenticalTo($field)
				->object($field->getOutputPrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetOutputColorizer()
	{
		$this
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setOutputColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getOutputColorizer())->isIdenticalTo($colorizer)
				->object($field->setOutputColorizer())->isIdenticalTo($field)
				->object($field->getOutputColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetLocale()
	{
		$this
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setLocale($locale = new atoum\locale()))->isIdenticalTo($field)
				->object($field->getLocale())->isIdenticalTo($locale)
			->if($field = new tests\uncompleted\cli(null, null, null, null, null, null, $locale = new atoum\locale()))
			->then
				->object($field->setLocale($locale = new atoum\locale()))->isIdenticalTo($field)
				->object($field->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new tests\uncompleted\cli())
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, new atoum\runner()))->isFalse()
				->variable($field->getRunner())->isNull()
				->boolean($field->handleEvent(atoum\runner::runStop, $runner = new atoum\runner()))->isTrue()
				->object($field->getRunner())->isIdenticalTo($runner)
		;
	}

	public function test__toString()
	{
		$this
			->if($score = new \mock\mageekguy\atoum\runner\score())
			->and($this->calling($score)->getUncompletedMethods = array())
			->and($runner = new atoum\runner())
			->and($runner->setScore($score))
			->and($defaultField = new tests\uncompleted\cli())
			->and($customField = new tests\uncompleted\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setOutputPrompt($outputPrompt = new prompt(uniqid())))
			->and($customField->setOutputColorizer($outputColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->then
				->castToString($defaultField)->isEmpty()
				->castToString($customField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($score->getMockController()->getUncompletedMethods = $allUncompletedMethods = array(
						array(
							'class' => $class = uniqid(),
							'method' => $method = uniqid(),
							'exitCode' => $exitCode = rand(1, PHP_INT_MAX),
							'output' => $output = uniqid()
						),
						array(
							'class' => $otherClass = uniqid(),
							'method' => $otherMethod = uniqid(),
							'exitCode' => $otherExitCode = rand(1, PHP_INT_MAX),
							'output' => ($otherOutputLine1 = uniqid()) . PHP_EOL . ($otherOutputLine2 = uniqid())
						),
						array(
							'class' => $anotherClass = uniqid(),
							'method' => $anotherMethod = uniqid(),
							'exitCode' => $anotherExitCode = rand(1, PHP_INT_MAX),
							'output' => ''
						)
					)
				)
			->and($defaultField = new tests\uncompleted\cli())
			->and($customField = new tests\uncompleted\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setOutputPrompt($outputPrompt = new prompt(uniqid())))
			->and($customField->setOutputColorizer($outputColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->then
				->castToString($defaultField)->isEmpty()
				->castToString($customField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('There are %d uncompleted methods:', sizeof($allUncompletedMethods)) . PHP_EOL .
						sprintf('%s::%s() with exit code %d:', $class, $method, $exitCode) . PHP_EOL .
						'output(' . strlen($output) . ') "' . $output . '"' . PHP_EOL .
						sprintf('%s::%s() with exit code %d:', $otherClass, $otherMethod, $otherExitCode) . PHP_EOL .
						'output(' . (strlen($otherOutputLine1 . PHP_EOL . $otherOutputLine2)) . ') "' . $otherOutputLine1 . PHP_EOL .
						$otherOutputLine2 . '"' . PHP_EOL .
						sprintf('%s::%s() with exit code %d:', $anotherClass, $anotherMethod, $anotherExitCode) . PHP_EOL .
						'output(0) ""' . PHP_EOL
					)
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($locale->__('There is %d uncompleted method', 'There are %d uncompleted methods', sizeof($allUncompletedMethods)), sizeof($allUncompletedMethods)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize(sprintf('%s::%s() with exit code %d', $class, $method, $exitCode))
					) .
					PHP_EOL .
					$outputPrompt .
					'output(' . strlen($output) . ') "' . $output . '"' .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize(sprintf('%s::%s() with exit code %d', $otherClass, $otherMethod, $otherExitCode))
					) .
					PHP_EOL .
					$outputPrompt .
					'output(' . (strlen($otherOutputLine1 . PHP_EOL . $otherOutputLine2)) . ') "' . $otherOutputLine1 .
					PHP_EOL .
					$outputPrompt .
					$otherOutputLine2 . '"' .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize(sprintf('%s::%s() with exit code %d', $anotherClass, $anotherMethod, $anotherExitCode))
					) .
					PHP_EOL .
					$outputPrompt .
					'output(0) ""' .
					PHP_EOL
				)
		;
	}
}
