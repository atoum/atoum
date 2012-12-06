<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\exceptions;

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
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\exceptions');
	}

	public function test__construct()
	{
		$this
			->if($field = new runner\exceptions\cli())
			->then
				->object($field->getTitlePrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getMethodPrompt())->isEqualTo(new prompt())
				->object($field->getMethodColorizer())->isEqualTo(new colorizer())
				->object($field->getExceptionPrompt())->isEqualTo(new prompt())
				->object($field->getExceptionColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getRunner())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetTitlePrompt()
	{
		$this
			->if($field = new runner\exceptions\cli())
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
			->if($field = new runner\exceptions\cli())
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
			->if($field = new runner\exceptions\cli())
			->then
				->object($field->setMethodPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getMethodPrompt())->isIdenticalTo($prompt)
				->object($field->setMethodPrompt())->isIdenticalTo($field)
				->object($field->getTitlePrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetMethodColorizer()
	{
		$this
			->if($field = new runner\exceptions\cli())
			->then
				->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
				->object($field->setMethodColorizer())->isIdenticalTo($field)
				->object($field->getTitleColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetExceptionPrompt()
	{
		$this
			->if($field = new runner\exceptions\cli())
			->then
				->object($field->setExceptionPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getExceptionPrompt())->isIdenticalTo($prompt)
				->object($field->setExceptionPrompt())->isIdenticalTo($field)
				->object($field->getTitlePrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetExceptionColorizer()
	{
		$this
			->if($field = new runner\exceptions\cli())
			->then
				->object($field->setExceptionColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getExceptionColorizer())->isIdenticalTo($colorizer)
				->object($field->setExceptionColorizer())->isIdenticalTo($field)
				->object($field->getTitleColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new runner\exceptions\cli())
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
			->if($score = new \mock\mageekguy\atoum\runner\score())
			->and($score->getMockController()->getExceptions = array())
			->and($runner = new atoum\runner())
			->and($runner->setScore($score))
			->and($field = new runner\exceptions\cli())
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($field)->isEmpty()
			->if($score->getMockController()->getExceptions = $exceptions = array(
						array(
							'class' => $class = uniqid(),
							'method' => $method = uniqid(),
							'file' => $file = uniqid(),
							'line' => $line = uniqid(),
							'value' => $value = uniqid()
						),
						array(
							'class' => $otherClass = uniqid(),
							'method' => $otherMethod = uniqid(),
							'file' => $otherFile = uniqid(),
							'line' => $otherLine = uniqid(),
							'value' => ($firstOtherValue = uniqid()) . PHP_EOL . ($secondOtherValue = uniqid())
						),
					)
				)
			->and($field = new runner\exceptions\cli())
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($field)->isEqualTo(sprintf('There are %d exceptions:', sizeof($exceptions)) . PHP_EOL .
					$class . '::' . $method . '():' . PHP_EOL .
					sprintf('An exception has been thrown in file %s on line %d:', $file, $line) . PHP_EOL .
					$value . PHP_EOL .
					$otherClass . '::' . $otherMethod . '():' . PHP_EOL .
					sprintf('An exception has been thrown in file %s on line %d:', $otherFile, $otherLine) . PHP_EOL .
					$firstOtherValue . PHP_EOL .
					$secondOtherValue . PHP_EOL
				)
			->if($field = new runner\exceptions\cli())
			->and($field->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($field->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($field->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($field->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($field->setExceptionPrompt($exceptionPrompt = new prompt(uniqid())))
			->and($field->setExceptionColorizer($exceptionColorizer = new colorizer(uniqid(), uniqid())))
			->and($field->setLocale($locale = new locale()))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($field)->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s:'),
						$titleColorizer->colorize(sprintf($field->getLocale()->__('There is %d exception', 'There are %d exceptions', sizeof($exceptions)), sizeof($exceptions)))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($class . '::' . $method . '()')
					) .
					PHP_EOL .
					$exceptionPrompt .
					sprintf(
						$locale->_('%s:'),
						$exceptionColorizer->colorize(sprintf($locale->_('An exception has been thrown in file %s on line %d'), $file, $line))
					) .
					PHP_EOL .
					$exceptionPrompt . $value . PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s:'),
						$methodColorizer->colorize($otherClass . '::' . $otherMethod . '()')
					) .
					PHP_EOL .
					$exceptionPrompt .
					sprintf(
						$locale->_('%s:'),
						$exceptionColorizer->colorize(sprintf($locale->_('An exception has been thrown in file %s on line %d'), $otherFile, $otherLine))
					) .
					PHP_EOL .
					$exceptionPrompt . $firstOtherValue . PHP_EOL .
					$exceptionPrompt . $secondOtherValue . PHP_EOL
				)
		;
	}
}
