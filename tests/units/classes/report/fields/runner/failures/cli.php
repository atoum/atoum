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
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\failures');
	}

	public function test__construct()
	{
		$this
			->if($field = new runner\failures\cli())
			->then
				->object($field->getTitlePrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMethodPrompt())->isEqualTo(new prompt())
				->object($field->getMethodColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getRunner())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetTitlePrompt()
	{
		$this
			->if($field = new runner\failures\cli())
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
			->if($field = new runner\failures\cli())
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
			->if($field = new runner\failures\cli())
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
			->if($field = new runner\failures\cli())
			->then
				->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
				->object($field->setTitleColorizer())->isIdenticalTo($field)
				->object($field->getTitleColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testHandleEvent()
	{
		$this
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
			->if($score = new \mock\mageekguy\atoum\runner\score())
			->and($score->getMockController()->getErrors = array())
			->and($runner = new atoum\runner())
			->and($runner->setScore($score))
			->and($defaultField = new runner\failures\cli())
			->and($customField = new runner\failures\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
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
			->and($customField = new runner\failures\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
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
			->and($customField = new runner\failures\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
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
							'dataSetProvider' => $dataSetProvider = uniqid(),
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
							'dataSetProvider' => $otherDataSetProvider = uniqid(),
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
			->and($customField = new runner\failures\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->then
				->castToString($defaultField)->isEmpty()
				->castToString($customField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->and($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('There are %d failures:', sizeof($fails)) . PHP_EOL .
					$class . '::' . $method . '():' . PHP_EOL .
					sprintf('In file %s on line %d in case \'%s\', %s failed for data set [%s] of data provider %s: %s', $file, $line, $case, $asserter, $dataSetKey, $dataSetProvider, $fail) . PHP_EOL .
					$otherClass . '::' . $otherMethod . '():' . PHP_EOL .
					sprintf('In file %s on line %d in case \'%s\', %s failed for data set [%s] of data provider %s: %s', $otherFile, $otherLine, $otherCase, $otherAsserter, $otherDataSetKey, $otherDataSetProvider, $otherFail) . PHP_EOL
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
					sprintf($locale->_('In file %s on line %d in case \'%s\', %s failed for data set [%s] of data provider %s: %s'), $file, $line, $case, $asserter, $dataSetKey, $dataSetProvider, $fail) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($otherClass . '::' . $otherMethod . '()')
					) .
					PHP_EOL .
					sprintf($locale->_('In file %s on line %d in case \'%s\', %s failed for data set [%s] of data provider %s: %s'), $otherFile, $otherLine, $otherCase, $otherAsserter, $otherDataSetKey, $otherDataSetProvider, $otherFail) .
					PHP_EOL
				)
		;
	}
}
