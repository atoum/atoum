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
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\report\fields\runner\tests\uncompleted')
		;
	}

	public function test__construct()
	{
		$this->assert
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
			->if($field = new tests\uncompleted\cli(null, null, null, null, null, null, null))
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
			->if($field = new tests\uncompleted\cli ($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(), $outputPrompt = new prompt(uniqid()), $outputColorizer = new colorizer(), $locale = new atoum\locale()))
			->then
				->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
				->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
				->object($field->getMethodPrompt())->isIdenticalTo($methodPrompt)
				->object($field->getMethodColorizer())->isIdenticalTo($methodColorizer)
				->object($field->getOutputPrompt())->isIdenticalTo($outputPrompt)
				->object($field->getOutputColorizer())->isIdenticalTo($outputColorizer)
				->object($field->getLocale())->isIdenticalTo($locale)
				->variable($field->getRunner())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetTitlePrompt()
	{
		$this->assert
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setTitlePrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isIdenticalTo($prompt)
			->if($field = new tests\uncompleted\cli(new prompt()))
			->then
				->object($field->setTitlePrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$this->assert
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
			->if($field = new tests\uncompleted\cli(null, new colorizer()))
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetMethodPrompt()
	{
		$this->assert
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setMethodPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getMethodPrompt())->isIdenticalTo($prompt)
			->if($field = new tests\uncompleted\cli(null, null, new prompt()))
			->then
				->object($field->setMethodPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getMethodPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetMethodColorizer()
	{
		$this->assert
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
			->if($field = new tests\uncompleted\cli(null, null, null, new colorizer()))
			->then
				->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetOutputPrompt()
	{
		$this->assert
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setOutputPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getOutputPrompt())->isIdenticalTo($prompt)
			->if($field = new tests\uncompleted\cli(null, null, null, null, new prompt()))
			->then
				->object($field->setOutputPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getOutputPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetOutputColorizer()
	{
		$this->assert
			->if($field = new tests\uncompleted\cli())
			->then
				->object($field->setOutputColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getOutputColorizer())->isIdenticalTo($colorizer)
			->if($field = new tests\uncompleted\cli(null, null, null, null, null, new colorizer()))
			->then
				->object($field->setOutputColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getOutputColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetLocale()
	{
		$this->assert
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
			->assert
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
			->assert
				->if($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getUncompletedMethods = array())
				->and($runner = new atoum\runner())
				->and($runner->setScore($score))
				->and($defaultField = new tests\uncompleted\cli())
				->then
					->castToString($defaultField)->isEmpty()
				->if($customField = new tests\uncompleted\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $outputPrompt = new prompt(uniqid()), $outputColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale()))
				->then
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
				->then
					->castToString($defaultField)->isEmpty()
				->if($customField = new tests\uncompleted\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $outputPrompt = new prompt(uniqid()), $outputColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale()))
				->then
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
