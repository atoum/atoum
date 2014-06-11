<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\errors;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner,
	mock\mageekguy\atoum as mock
;

require_once __DIR__ . '/../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\errors');
	}

	public function test__construct()
	{
		$this
			->if($field = new runner\errors\cli())
			->then
				->object($field->getTitlePrompt())->isEqualTo(new prompt())
				->object($field->getMethodPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMethodColorizer())->isEqualTo(new colorizer())
				->object($field->getErrorPrompt())->isEqualTo(new prompt())
				->object($field->getErrorColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getRunner())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetTitlePrompt()
	{
		$this
			->if($field = new runner\errors\cli())
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
			->if($field = new runner\errors\cli())
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
			->if($field = new runner\errors\cli())
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
			->if($field = new runner\errors\cli())
			->then
				->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
				->object($field->setMethodColorizer())->isIdenticalTo($field)
				->object($field->getMethodColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetErrorPrompt()
	{
		$this
			->if($field = new runner\errors\cli())
			->then
				->object($field->setErrorPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getErrorPrompt())->isIdenticalTo($prompt)
				->object($field->setErrorPrompt())->isIdenticalTo($field)
				->object($field->getErrorPrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetErrorColorizer()
	{
		$this
			->if($field = new runner\errors\cli())
			->then
				->object($field->setErrorColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getErrorColorizer())->isIdenticalTo($colorizer)
				->object($field->setErrorColorizer())->isIdenticalTo($field)
				->object($field->getErrorColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new runner\errors\cli())
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, new atoum\runner()))->isFalse()
				->variable($field->getRunner())->isNull()
			->if($runner = new atoum\runner())
			->then
				->boolean($field->handleEvent(atoum\runner::runStop, $runner))->isTrue()
				->object($field->getRunner())->isIdenticalTo($runner)
		;
	}

	public function test__toString()
	{
		$this
			->if($runner = new mock\runner())
			->and($runner->getMockController()->getScore = $score = new mock\score())
			->and($defaultField = new runner\errors\cli())
			->and($customField = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale()))
			->and($score->getMockController()->getErrors = array())
			->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->and($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($score->getMockController()->getErrors = $allErrors = array(
						array(
							'case' => null,
							'class' => $class = uniqid(),
							'method' => $method = uniqid(),
							'file' => $file = uniqid(),
							'line' => $line = rand(1, PHP_INT_MAX),
							'type' => $type = 'e_fake_error',
							'message' => $message = uniqid(),
							'errorFile' => $errorFile = uniqid(),
							'errorLine' => $errorLine = rand(1, PHP_INT_MAX),
						),
						array(
							'case' => null,
							'class' => $otherClass = uniqid(),
							'method' => $otherMethod = uniqid(),
							'file' => $otherFile = uniqid(),
							'line' => $otherLine = rand(1, PHP_INT_MAX),
							'type' => $otherType = 'e_other_fake_error',
							'message' => ($firstOtherMessage = uniqid()) . PHP_EOL . ($secondOtherMessage = uniqid()),
							'errorFile' => $otherErrorFile = uniqid(),
							'errorLine' => $otherErrorLine = rand(1, PHP_INT_MAX),
						)
					)
				)
			->and($defaultField = new runner\errors\cli())
			->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(
					sprintf('There are %d errors:', sizeof($allErrors)) . PHP_EOL .
					$class . '::' . $method . '():' . PHP_EOL .
					sprintf('Error %s in %s on line %d, generated by file %s on line %d:', strtoupper($type), $file, $line, $errorFile, $errorLine) . PHP_EOL .
					$message . PHP_EOL .
					$otherClass . '::' . $otherMethod . '():' . PHP_EOL .
					sprintf('Error %s in %s on line %d, generated by file %s on line %d:', strtoupper($otherType), $otherFile, $otherLine, $otherErrorFile, $otherErrorLine) . PHP_EOL .
					$firstOtherMessage . PHP_EOL .
					$secondOtherMessage . PHP_EOL
				)
			->if($customField = new runner\errors\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setErrorPrompt($errorPrompt = new prompt(uniqid())))
			->and($customField->setErrorColorizer($errorColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->and($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($class . '::' . $method . '()')
					) .
					PHP_EOL .
					$errorPrompt .
					sprintf(
						$locale->_('%s:'),
						$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on line %d, generated by file %s on line %d'), strtoupper($type), $file, $line, $errorFile, $errorLine))
					) .
					PHP_EOL .
					$message . PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($otherClass . '::' . $otherMethod . '()')
					) .
					PHP_EOL .
					$errorPrompt .
					sprintf(
						$locale->_('%s:'),
						$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on line %d, generated by file %s on line %d'), strtoupper($otherType), $otherFile, $otherLine, $otherErrorFile, $otherErrorLine))
					) .
					PHP_EOL .
					$firstOtherMessage . PHP_EOL .
					$secondOtherMessage . PHP_EOL
				)
			->if($score->getMockController()->getErrors = $allErrors = array(
					array(
						'case' => $case = uniqid(),
						'class' => $class = uniqid(),
						'method' => $method = uniqid(),
						'file' => $file = uniqid(),
						'line' => $line = rand(1, PHP_INT_MAX),
						'type' => $type = rand(1, PHP_INT_MAX),
						'message' => $message = uniqid(),
						'errorFile' => $errorFile = uniqid(),
						'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
					),
					array(
						'case' => $otherCase = uniqid(),
						'class' => $otherClass = uniqid(),
						'method' => $otherMethod = uniqid(),
						'file' => $otherFile = uniqid(),
						'line' => $otherLine = rand(1, PHP_INT_MAX),
						'type' => $otherType = rand(1, PHP_INT_MAX),
						'message' => ($firstOtherMessage = uniqid()) . PHP_EOL . ($secondOtherMessage = uniqid()),
						'errorFile' => $otherErrorFile = uniqid(),
						'errorLine' => $otherErrorLine = rand(1, PHP_INT_MAX)
					)
				)
			)
			->and($defaultField = new runner\errors\cli())
			->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('There are %d errors:', sizeof($allErrors)) . PHP_EOL .
					$class . '::' . $method . '():' . PHP_EOL .
					sprintf('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':', strtoupper($type), $file, $line, $errorFile, $errorLine, $case) . PHP_EOL .
					$message . PHP_EOL .
					$otherClass . '::' . $otherMethod . '():' . PHP_EOL .
					sprintf('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':', strtoupper($otherType), $otherFile, $otherLine, $otherErrorFile, $otherErrorLine, $otherCase) . PHP_EOL .
					$firstOtherMessage . PHP_EOL .
					$secondOtherMessage . PHP_EOL
				)
			->if($customField = new runner\errors\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setErrorPrompt($errorPrompt = new prompt(uniqid())))
			->and($customField->setErrorColorizer($errorColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->and($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($class . '::' . $method . '()')
					) .
					PHP_EOL .
					$errorPrompt .
					sprintf(
						$locale->_('%s:'),
						$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\''), strtoupper($type), $file, $line, $errorFile, $errorLine, $case))
					) .
					PHP_EOL .
					$message . PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($otherClass . '::' . $otherMethod . '()')
					) .
					PHP_EOL .
					$errorPrompt .
					sprintf(
						$locale->_('%s:'),
						$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\''), strtoupper($otherType), $otherFile, $otherLine, $otherErrorFile, $otherErrorLine, $otherCase))
					) .
					PHP_EOL .
					$firstOtherMessage . PHP_EOL .
					$secondOtherMessage . PHP_EOL
				)
			->if($score->getMockController()->getErrors = $allErrors = array(
						array(
							'case' => null,
							'class' => $class = uniqid(),
							'method' => $method = uniqid(),
							'file' => null,
							'line' => null,
							'type' => $type = rand(1, PHP_INT_MAX),
							'message' => $message = uniqid(),
							'errorFile' => $errorFile = uniqid(),
							'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
						)
					)
				)
			->and($defaultField = new runner\errors\cli())
			->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
					$class . '::' . $method . '():' . PHP_EOL .
					sprintf('Error %s in unknown file on unknown line, generated by file %s on line %d:', strtoupper($type), $errorFile, $errorLine) . PHP_EOL .
					$message . PHP_EOL
				)
			->if($customField = new runner\errors\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setErrorPrompt($errorPrompt = new prompt(uniqid())))
			->and($customField->setErrorColorizer($errorColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->and($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($class . '::' . $method . '()')
					) .
					PHP_EOL .
					$errorPrompt .
					sprintf(
						$locale->_('%s:'),
						$errorColorizer->colorize(sprintf($locale->_('Error %s in unknown file on unknown line, generated by file %s on line %d'), strtoupper($type), $errorFile, $errorLine))
					) .
					PHP_EOL .
					$message . PHP_EOL
				)
			->if($score->getMockController()->getErrors = $allErrors = array(
					array(
						'case' => $case = uniqid(),
						'class' => $class = uniqid(),
						'method' => $method = uniqid(),
						'file' => null,
						'line' => null,
						'type' => $type = rand(1, PHP_INT_MAX),
						'message' => $message = uniqid(),
						'errorFile' => $errorFile = uniqid(),
						'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
					)
				)
			)
			->and($defaultField = new runner\errors\cli())
			->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
					$class . '::' . $method . '():' . PHP_EOL .
					sprintf('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':', strtoupper($type), $errorFile, $errorLine, $case) . PHP_EOL .
					$message . PHP_EOL
				)
			->if($customField = new runner\errors\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setErrorPrompt($errorPrompt = new prompt(uniqid())))
			->and($customField->setErrorColorizer($errorColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->and($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($class . '::' . $method . '()')
					) .
					PHP_EOL .
					$errorPrompt .
					sprintf(
						$locale->_('%s:'),
						$errorColorizer->colorize(sprintf($locale->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\''), strtoupper($type), $errorFile, $errorLine, $case))
					) .
					PHP_EOL .
					$message . PHP_EOL
				)
			->if($score->getMockController()->getErrors = $allErrors = array(
					array(
						'case' => null,
						'class' => $class = uniqid(),
						'method' => $method = uniqid(),
						'file' => null,
						'line' => $line = rand(1, PHP_INT_MAX),
						'type' => $type = 'e_fake_error',
						'message' => $message = uniqid(),
						'errorFile' => $errorFile = uniqid(),
						'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
					)
				)
			)
			->and($defaultField = new runner\errors\cli())
			->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
					$class . '::' . $method . '():' . PHP_EOL .
					sprintf('Error %s in unknown file on unknown line, generated by file %s on line %d:', strtoupper($type), $errorFile, $errorLine) . PHP_EOL .
					$message . PHP_EOL
				)
			->if($customField = new runner\errors\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setErrorPrompt($errorPrompt = new prompt(uniqid())))
			->and($customField->setErrorColorizer($errorColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->and($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($class . '::' . $method . '()')
					) .
					PHP_EOL .
					$errorPrompt .
					sprintf(
						$locale->_('%s:'),
						$errorColorizer->colorize(sprintf($locale->_('Error %s in unknown file on unknown line, generated by file %s on line %d'), strtoupper($type), $errorFile, $errorLine))
					) .
					PHP_EOL .
					$message . PHP_EOL
				)
			->if($score->getMockController()->getErrors = $allErrors = array(
					array(
						'case' => $case = uniqid(),
						'class' => $class = uniqid(),
						'method' => $method = uniqid(),
						'file' => $file = uniqid(),
						'line' => null,
						'type' => $type = 'e_fake_error',
						'message' => $message = uniqid(),
						'errorFile' => null,
						'errorLine' => null
					)
				)
			)
			->and($defaultField = new runner\errors\cli())
			->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
					$class . '::' . $method . '():' . PHP_EOL .
					sprintf('Error %s in %s on unknown line, generated by unknown file in case \'%s\':', strtoupper($type), $file, $case) . PHP_EOL .
					$message . PHP_EOL
				)
			->if($customField = new runner\errors\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setErrorPrompt($errorPrompt = new prompt(uniqid())))
			->and($customField->setErrorColorizer($errorColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->and($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($class . '::' . $method . '()')
					) .
					PHP_EOL .
					$errorPrompt .
					sprintf(
						$locale->_('%s:'),
						$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on unknown line, generated by unknown file in case \'%s\''), strtoupper($type), $file, $case))
					) .
					PHP_EOL .
					$message . PHP_EOL
				)
			->if($score->getMockController()->getErrors = $allErrors = array(
						array(
							'case' => $case = uniqid(),
							'class' => $class = uniqid(),
							'method' => $method = uniqid(),
							'file' => null,
							'line' => $line = rand(1, PHP_INT_MAX),
							'type' => $type = 'e_fake_error',
							'message' => $message = uniqid(),
							'errorFile' => $errorFile = uniqid(),
							'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
						)
					)
				)
			->and($defaultField = new runner\errors\cli())
			->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
					$class . '::' . $method . '():' . PHP_EOL .
					sprintf('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':', strtoupper($type), $errorFile, $errorLine, $case) . PHP_EOL .
					$message . PHP_EOL
				)
			->if($customField = new runner\errors\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setErrorPrompt($errorPrompt = new prompt(uniqid())))
			->and($customField->setErrorColorizer($errorColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->and($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($class . '::' . $method . '()')
					) .
					PHP_EOL .
					$errorPrompt .
					sprintf(
						$locale->_('%s:'),
						$errorColorizer->colorize(sprintf($locale->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\''), strtoupper($type), $errorFile, $errorLine, $case))
					) .
					PHP_EOL .
					$message . PHP_EOL
				)
			->if($score->getMockController()->getErrors = $allErrors = array(
						array(
							'case' => null,
							'class' => $class = uniqid(),
							'method' => $method = uniqid(),
							'file' => $file = uniqid(),
							'line' => null,
							'type' => $type = 'e_fake_error',
							'message' => $message = uniqid(),
							'errorFile' => $errorFile = uniqid(),
							'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
						)
					)
				)
			->and($defaultField = new runner\errors\cli())
			->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
					$class . '::' . $method . '():' . PHP_EOL .
					sprintf('Error %s in %s on unknown line, generated by file %s on line %d:', strtoupper($type), $file, $errorFile, $errorLine) . PHP_EOL .
					$message . PHP_EOL
				)
			->if($customField = new runner\errors\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setErrorPrompt($errorPrompt = new prompt(uniqid())))
			->and($customField->setErrorColorizer($errorColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->and($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($class . '::' . $method . '()')
					) .
					PHP_EOL .
					$errorPrompt .
					sprintf(
						$locale->_('%s:'),
						$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on unknown line, generated by file %s on line %d'), strtoupper($type), $file, $errorFile, $errorLine))
					) .
					PHP_EOL .
					$message . PHP_EOL
				)
			->if($score->getMockController()->getErrors = $allErrors = array(
						array(
							'case' => $case = uniqid(),
							'class' => $class = uniqid(),
							'method' => $method = uniqid(),
							'file' => $file = uniqid(),
							'line' => null,
							'type' => $type = 'e_fake_error',
							'message' => $message = uniqid(),
							'errorFile' => $errorFile = uniqid(),
							'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
						)
					)
				)
			->and($defaultField = new runner\errors\cli())
			->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEmpty()
			->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
					$class . '::' . $method . '():' . PHP_EOL .
					sprintf('Error %s in %s on unknown line, generated by file %s on line %d in case \'%s\':', strtoupper($type), $file, $errorFile, $errorLine, $case) . PHP_EOL .
					$message . PHP_EOL
				)
			->if($customField = new runner\errors\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setErrorPrompt($errorPrompt = new prompt(uniqid())))
			->and($customField->setErrorColorizer($errorColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new atoum\locale()))
			->and($customField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($customField)->isEmpty()
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($class . '::' . $method . '()')
					) .
					PHP_EOL .
					$errorPrompt .
					sprintf(
						$locale->_('%s:'),
						$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on unknown line, generated by file %s on line %d in case \'%s\''), strtoupper($type), $file, $errorFile, $errorLine, $case))
					) .
					PHP_EOL .
					$message . PHP_EOL
				)
		;
	}
}
