<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\skipped;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner\tests\skipped\cli as testedClass
;

require __DIR__ . '/../../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\tests\skipped');
	}

	public function testSetTitlePrompt()
	{
		$this
			->if($field = new testedClass())
			->then
				->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
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
			->if($field = new testedClass())
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
			->if($field = new testedClass())
			->then
				->object($field->setMethodPrompt($prompt = new prompt()))->isIdenticalTo($field)
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
			->if($field = new testedClass())
			->then
				->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
				->object($field->setMethodColorizer())->isIdenticalTo($field)
				->object($field->getMethodColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetMessageColorizer()
	{
		$this
			->if($field = new testedClass())
			->then
				->object($field->setMessageColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMessageColorizer())->isIdenticalTo($colorizer)
				->object($field->setMessageColorizer())->isIdenticalTo($field)
				->object($field->getMessageColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function test__toString()
	{
		$this
			->if($score = new \mock\mageekguy\atoum\runner\score())
			->and($this->calling($score)->getSkippedMethods = array())
			->and($runner = new atoum\runner())
			->and($runner->setScore($score))
			->and($defaultField = new testedClass())
			->and($customField = new testedClass())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMessageColorizer($messageColorizer = new colorizer(uniqid(), uniqid())))
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
			->if($score->getMockController()->getSkippedMethods = $allSkippedMethods = array(
						array(
							'class' => $class = uniqid(),
							'method' => $method = uniqid(),
							'message' => $message = uniqid()
						),
						array(
							'class' => $otherClass = uniqid(),
							'method' => $otherMethod = uniqid(),
							'message' => $otherMessage = uniqid()
						),
						array(
							'class' => $anotherClass = uniqid(),
							'method' => $anotherMethod = uniqid(),
							'message' => $anotherMessage = uniqid()
						)
					)
				)
			->and($defaultField = new testedClass())
			->and($customField = new testedClass())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMessageColorizer($messageColorizer = new colorizer(uniqid(), uniqid())))
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
				->castToString($defaultField)->isEqualTo(sprintf('There are %d skipped methods:', sizeof($allSkippedMethods)) . PHP_EOL .
						sprintf('%s::%s(): %s', $class, $method, $message) . PHP_EOL .
						sprintf('%s::%s(): %s', $otherClass, $otherMethod, $otherMessage) . PHP_EOL .
						sprintf('%s::%s(): %s', $anotherClass, $anotherMethod, $anotherMessage) . PHP_EOL
					)
			->if($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($locale->__('There is %d skipped method', 'There are %d skipped methods', sizeof($allSkippedMethods)), sizeof($allSkippedMethods)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s: %s'),
						$methodColorizer->colorize(sprintf('%s::%s()', $class, $method)),
						$messageColorizer->colorize($message)
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s: %s'),
						$methodColorizer->colorize(sprintf('%s::%s()', $otherClass, $otherMethod)),
						$messageColorizer->colorize($otherMessage)
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s: %s'),
						$methodColorizer->colorize(sprintf('%s::%s()', $anotherClass, $anotherMethod)),
						$messageColorizer->colorize($anotherMessage)
					) .
					PHP_EOL
				)
		;
	}
}
