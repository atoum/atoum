<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\outputs;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\tests\units,
	mageekguy\atoum\report\fields\runner\outputs
;

require_once __DIR__ . '/../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\outputs');
	}

	public function test__construct()
	{
		$this
			->if($field = new outputs\cli())
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
			->if($field = new outputs\cli())
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
			->if($field = new outputs\cli())
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
			->if($field = new outputs\cli())
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
			->if($field = new outputs\cli())
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
			->if($field = new outputs\cli())
			->then
				->object($field->setOutputPrompt($prompt = new prompt()))->isIdenticalTo($field)
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
			->if($field = new outputs\cli())
			->then
				->object($field->setOutputColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getOutputColorizer())->isIdenticalTo($colorizer)
				->object($field->setOutputColorizer())->isIdenticalTo($field)
				->object($field->getOutputColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new outputs\cli())
			->and($runner = new atoum\runner())
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, $runner))->isFalse()
				->variable($field->getRunner())->isNull()
				->boolean($field->handleEvent(atoum\runner::runStop, $runner))->isTrue()
				->object($field->getRunner())->isIdenticalTo($runner)
		;
	}

	public function test__toString()
	{
		$this
			->if($score = new \mock\mageekguy\atoum\runner\score())
			->and($score->getMockController()->getOutputs = array())
			->and($runner = new atoum\runner())
			->and($runner->setScore($score))
			->and($defaultField = new outputs\cli())
			->and($customField = new outputs\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setOutputPrompt($outputPrompt = new prompt(uniqid())))
			->and($customField->setOutputColorizer($outputColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new locale()))
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
			->if($score->getMockController()->getOutputs = $fields = array(
						array(
							'class' => $class = uniqid(),
							'method' => $method = uniqid(),
							'value' => $value = uniqid()
						),
						array(
							'class' => $otherClass = uniqid(),
							'method' => $otherMethod = uniqid(),
							'value' => ($firstOtherValue = uniqid()) . PHP_EOL . ($secondOtherValue = uniqid())
						)
					)
				)
			->and($defaultField = new outputs\cli())
			->and($customField = new outputs\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setOutputPrompt($outputPrompt = new prompt(uniqid())))
			->and($customField->setOutputColorizer($outputColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new locale()))
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
				->castToString($defaultField)->isEqualTo(sprintf('There are %d outputs:', sizeof($fields)) . PHP_EOL .
						'In ' . $class . '::' . $method . '():' . PHP_EOL .
						$value . PHP_EOL .
						'In ' . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
						$firstOtherValue . PHP_EOL .
						$secondOtherValue . PHP_EOL
					)
				->castToString($customField)->isEqualTo(
						$titlePrompt .
						sprintf(
							$locale->_('%s:'),
							$titleColorizer->colorize(sprintf($locale->__('There is %d output', 'There are %d outputs', sizeof($fields)), sizeof($fields)))
						) .
						PHP_EOL .
						$methodPrompt .
						sprintf(
							$locale->_('%s:'),
							$methodColorizer->colorize('In ' . $class . '::' . $method . '()')
						) .
						PHP_EOL .
						$outputPrompt .
						$outputColorizer->colorize($value) . PHP_EOL .
						$methodPrompt .
						sprintf(
							$locale->_('%s:'),
							$methodColorizer->colorize('In ' . $otherClass . '::' . $otherMethod . '()')
						) .
						PHP_EOL .
						$outputPrompt . $outputColorizer->colorize($firstOtherValue) . PHP_EOL .
						$outputPrompt . $outputColorizer->colorize($secondOtherValue) . PHP_EOL
					)
			->if($score->getMockController()->getOutputs = $fields = array(
						array(
							'class' => $class = uniqid(),
							'method' => $method = uniqid(),
							'value' => $value = uniqid()
						),
						array(
							'class' => $otherClass = uniqid(),
							'method' => $otherMethod = uniqid(),
							'value' => ($firstOtherValue = uniqid()) . PHP_EOL . ($secondOtherValue = uniqid())
						)
					)
				)
			->and($defaultField = new outputs\cli())
			->and($customField = new outputs\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setMethodColorizer($methodColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setOutputPrompt($outputPrompt = new prompt(uniqid())))
			->and($customField->setOutputColorizer($outputColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new locale()))
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
				->castToString($defaultField)->isEqualTo(sprintf('There are %d outputs:', sizeof($fields)) . PHP_EOL .
						'In ' . $class . '::' . $method . '():' . PHP_EOL .
						$value . PHP_EOL .
						'In ' . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
						$firstOtherValue . PHP_EOL .
						$secondOtherValue . PHP_EOL
					)
			->then
				->castToString($customField)->isEqualTo(
						$titlePrompt .
						sprintf(
							$locale->_('%s:'),
							$titleColorizer->colorize(sprintf($locale->__('There is %d output', 'There are %d outputs', sizeof($fields)), sizeof($fields)))
						) .
						PHP_EOL .
						$methodPrompt .
						sprintf(
							$locale->_('%s:'),
							$methodColorizer->colorize('In ' . $class . '::' . $method . '()')
						) .
						PHP_EOL .
						$outputPrompt .
						$outputColorizer->colorize($value) . PHP_EOL .
						$methodPrompt .
						sprintf(
							$locale->_('%s:'),
							$methodColorizer->colorize('In ' . $otherClass . '::' . $otherMethod . '()')
						) .
						PHP_EOL .
						$outputPrompt . $outputColorizer->colorize($firstOtherValue) . PHP_EOL .
						$outputPrompt . $outputColorizer->colorize($secondOtherValue) . PHP_EOL
					)
		;
	}
}
