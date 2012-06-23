<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\failures;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\tests\units,
	mageekguy\atoum\report\fields\runner
;

require_once __DIR__ . '/../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\report\fields\runner\failures')
		;
	}

	public function test__construct()
	{

		$this->assert
			->if($field = new runner\failures\cli())
			->then
				->object($field->getTitlePrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMethodPrompt())->isEqualTo(new prompt())
				->object($field->getMethodColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getRunner())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
			->if($field = new runner\failures\cli(null, null, null, null, null))
			->then
				->object($field->getTitlePrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMethodPrompt())->isEqualTo(new prompt())
				->object($field->getMethodColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getRunner())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
			->if($field = new runner\failures\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(), $locale = new atoum\locale()))
			->then
				->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
				->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
				->object($field->getMethodPrompt())->isIdenticalTo($methodPrompt)
				->object($field->getMethodColorizer())->isIdenticalTo($methodColorizer)
				->variable($field->getRunner())->isNull()
				->object($field->getLocale())->isIdenticalTo($locale)
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetTitlePrompt()
	{

		$this->assert
			->if($field = new runner\failures\cli())
			->then
				->object($field->setTitlePrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isIdenticalTo($prompt)
			->if($field = new runner\failures\cli(new prompt(uniqid())))
			->then
				->object($field->setTitlePrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$this->assert
			->if($field = new runner\failures\cli())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
			->if($field = new runner\failures\cli(null, new colorizer()))
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetMethodPrompt()
	{
		$this->assert
			->if($field = new runner\failures\cli())
			->then
				->object($field->setMethodPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getMethodPrompt())->isIdenticalTo($prompt)
			->if($field = new runner\failures\cli(null, null, new prompt()))
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetMethodColorizer()
	{
		$this->assert
			->if($field = new runner\failures\cli())
			->then
				->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
			->if($field = new runner\failures\cli(null, null, null, new colorizer()))
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testHandleEvent()
	{
		$this
			->assert
				->if($field = new runner\failures\cli())
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
				->and($score->getMockController()->getErrors = array())
				->and($runner = new atoum\runner())
				->and($runner->setScore($score))
				->and($defaultField = new runner\failures\cli())
				->and($customField = new runner\failures\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale()))
				->then
					->castToString($defaultField)->isEmpty()
					->castToString($customField)->isEmpty()
				->if($defaultField->handleEvent(atoum\runner::runStart, $runner))
				->and($customField->handleEvent(atoum\runner::runStart, $runner))
				->then
					->castToString($defaultField)->isEmpty()
					->castToString($customField)->isEmpty()
				->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
				->and($customField->handleEvent(atoum\runner::runStop, $runner))
				->then
					->castToString($defaultField)->isEmpty()
					->castToString($customField)->isEmpty()
				->if($score->getMockController()->getFailAssertions = $fails = array(
							array(
								'case' => null,
								'dataSetKey' => null,
								'class' => $class = uniqid(),
								'method' => $method = uniqid(),
								'file' => $file = uniqid(),
								'line' => $line = uniqid(),
								'asserter' => $asserter = uniqid(),
								'fail' => $fail = uniqid()
							),
							array(
								'case' => null,
								'dataSetKey' => null,
								'class' => $otherClass = uniqid(),
								'method' => $otherMethod = uniqid(),
								'file' => $otherFile = uniqid(),
								'line' => $otherLine = uniqid(),
								'asserter' => $otherAsserter = uniqid(),
								'fail' => $otherFail = uniqid()
							)
						)
					)
				->and($defaultField = new runner\failures\cli())
				->and($customField = new runner\failures\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale()))
				->then
					->castToString($defaultField)->isEmpty()
					->castToString($customField)->isEmpty()
				->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
				->and($customField->handleEvent(atoum\runner::runStop, $runner))
				->then
					->castToString($defaultField)->isEqualTo(sprintf('There are %d failures:', sizeof($fails)) . PHP_EOL .
						$class . '::' . $method . '():' . PHP_EOL .
						sprintf('In file %s on line %d, %s failed: %s', $file, $line, $asserter, $fail) . PHP_EOL .
						$otherClass . '::' . $otherMethod . '():' . PHP_EOL .
						sprintf('In file %s on line %d, %s failed: %s', $otherFile, $otherLine, $otherAsserter, $otherFail) . PHP_EOL
					)
					->castToString($customField)->isEqualTo(
						$titlePrompt .
						sprintf(
							$locale->_('%s:'),
							$titleColorizer->colorize(sprintf($locale->__('There is %d failure', 'There are %d failures', sizeof($fails)), sizeof($fails)))
						) .
						PHP_EOL .
						$methodPrompt .
						sprintf(
							$locale->_('%s:'),
							$methodColorizer->colorize($class . '::' . $method . '()')
						) .
						PHP_EOL .
						sprintf($locale->_('In file %s on line %d, %s failed: %s'), $file, $line, $asserter, $fail) .
						PHP_EOL .
						$methodPrompt .
						sprintf(
							$locale->_('%s:'),
							$methodColorizer->colorize($otherClass . '::' . $otherMethod . '()')
						) .
						PHP_EOL .
						sprintf($locale->_('In file %s on line %d, %s failed: %s'), $otherFile, $otherLine, $otherAsserter, $otherFail) .
						PHP_EOL
					)
				->if($score->getMockController()->getFailAssertions = $fails = array(
							array(
								'case' => $case =  uniqid(),
								'dataSetKey' => null,
								'class' => $class = uniqid(),
								'method' => $method = uniqid(),
								'file' => $file = uniqid(),
								'line' => $line = uniqid(),
								'asserter' => $asserter = uniqid(),
								'fail' => $fail = uniqid()
							),
							array(
								'case' => $otherCase =  uniqid(),
								'dataSetKey' => null,
								'class' => $otherClass = uniqid(),
								'method' => $otherMethod = uniqid(),
								'file' => $otherFile = uniqid(),
								'line' => $otherLine = uniqid(),
								'asserter' => $otherAsserter = uniqid(),
								'fail' => $otherFail = uniqid()
							)
						)
					)
				->and($defaultField = new runner\failures\cli())
				->and($customField = new runner\failures\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale()))
				->then
					->castToString($defaultField)->isEmpty()
					->castToString($customField)->isEmpty()
				->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
				->and($customField->handleEvent(atoum\runner::runStop, $runner))
				->then
					->castToString($defaultField)->isEqualTo(sprintf('There are %d failures:', sizeof($fails)) . PHP_EOL .
						$class . '::' . $method . '():' . PHP_EOL .
						sprintf('In file %s on line %d in case \'%s\', %s failed: %s', $file, $line, $case, $asserter, $fail) . PHP_EOL .
						$otherClass . '::' . $otherMethod . '():' . PHP_EOL .
						sprintf('In file %s on line %d in case \'%s\', %s failed: %s', $otherFile, $otherLine, $otherCase, $otherAsserter, $otherFail) . PHP_EOL
					)
					->castToString($customField)->isEqualTo(
						$titlePrompt .
						sprintf(
							$locale->_('%s:'),
							$titleColorizer->colorize(sprintf($locale->__('There is %d failure', 'There are %d failures', sizeof($fails)), sizeof($fails)))
						) .
						PHP_EOL .
						$methodPrompt .
						sprintf(
							$locale->_('%s:'),
							$methodColorizer->colorize($class . '::' . $method . '()')
						) .
						PHP_EOL .
						sprintf($locale->_('In file %s on line %d in case \'%s\', %s failed: %s'), $file, $line, $case, $asserter, $fail) .
						PHP_EOL .
						$methodPrompt .
						sprintf(
							$locale->_('%s:'),
							$methodColorizer->colorize($otherClass . '::' . $otherMethod . '()')
						) .
						PHP_EOL .
						sprintf($locale->_('In file %s on line %d in case \'%s\', %s failed: %s'), $otherFile, $otherLine, $otherCase, $otherAsserter, $otherFail) .
						PHP_EOL
					)
				->if($score->getMockController()->getFailAssertions = $fails = array(
							array(
								'case' => $case =  uniqid(),
								'dataSetKey' => $dataSetKey = rand(1, PHP_INT_MAX),
								'class' => $class = uniqid(),
								'method' => $method = uniqid(),
								'file' => $file = uniqid(),
								'line' => $line = uniqid(),
								'asserter' => $asserter = uniqid(),
								'fail' => $fail = uniqid()
							),
							array(
								'case' => $otherCase =  uniqid(),
								'dataSetKey' => $otherDataSetKey = rand(1, PHP_INT_MAX),
								'class' => $otherClass = uniqid(),
								'method' => $otherMethod = uniqid(),
								'file' => $otherFile = uniqid(),
								'line' => $otherLine = uniqid(),
								'asserter' => $otherAsserter = uniqid(),
								'fail' => $otherFail = uniqid()
							)
						)
					)
				->and($defaultField = new runner\failures\cli())
				->and($customField = new runner\failures\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale()))
				->then
					->castToString($defaultField)->isEmpty()
					->castToString($customField)->isEmpty()
				->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
				->and($customField->handleEvent(atoum\runner::runStop, $runner))
				->then
					->castToString($defaultField)->isEqualTo(sprintf('There are %d failures:', sizeof($fails)) . PHP_EOL .
						$class . '::' . $method . '():' . PHP_EOL .
						sprintf('In file %s on line %d in case \'%s\', %s failed for data set #%s: %s', $file, $line, $case, $asserter, $dataSetKey, $fail) . PHP_EOL .
						$otherClass . '::' . $otherMethod . '():' . PHP_EOL .
						sprintf('In file %s on line %d in case \'%s\', %s failed for data set #%s: %s', $otherFile, $otherLine, $otherCase, $otherAsserter, $otherDataSetKey, $otherFail) . PHP_EOL
					)
					->castToString($customField)->isEqualTo(
						$titlePrompt .
						sprintf(
							$locale->_('%s:'),
							$titleColorizer->colorize(sprintf($locale->__('There is %d failure', 'There are %d failures', sizeof($fails)), sizeof($fails)))
						) .
						PHP_EOL .
						$methodPrompt .
						sprintf(
							$locale->_('%s:'),
							$methodColorizer->colorize($class . '::' . $method . '()')
						) .
						PHP_EOL .
						sprintf($locale->_('In file %s on line %d in case \'%s\', %s failed for data set #%s: %s'), $file, $line, $case, $asserter, $dataSetKey, $fail) .
						PHP_EOL .
						$methodPrompt .
						sprintf(
							$locale->_('%s:'),
							$methodColorizer->colorize($otherClass . '::' . $otherMethod . '()')
						) .
						PHP_EOL .
						sprintf($locale->_('In file %s on line %d in case \'%s\', %s failed for data set #%s: %s'), $otherFile, $otherLine, $otherCase, $otherAsserter, $otherDataSetKey, $otherFail) .
						PHP_EOL
					)
		;
	}
}
