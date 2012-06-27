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
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\report\fields\runner\outputs')
		;
	}

	public function test__construct()
	{
		$this->assert
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
			->if($field = new outputs\cli(null, null, null, null, null, null, null))
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
			->if($field = new outputs\cli($titlePrompt = new prompt(), $titleColorizer = new colorizer(), $methodPrompt = new prompt(), $methodColorizer = new colorizer(), $outputPrompt = new prompt(), $outputColorizer = new colorizer(), $locale = new locale()))
			->then
				->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
				->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
				->object($field->getMethodPrompt())->isIdenticalTo($methodPrompt)
				->object($field->getMethodColorizer())->isIdenticalTo($methodColorizer)
				->object($field->getLocale())->isIdenticalTo($locale)
				->variable($field->getRunner())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetTitlePrompt()
	{
		$this->assert
			->if($field = new outputs\cli())
			->then
				->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isIdenticalTo($prompt)
			->if($field = new outputs\cli(new prompt()))
			->then
				->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$this->assert
			->if($field = new outputs\cli())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
			->if($field = new outputs\cli(null, new colorizer()))
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetMethodPrompt()
	{
		$this->assert
			->if($field = new outputs\cli())
			->then
				->object($field->setMethodPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getMethodPrompt())->isIdenticalTo($prompt)
			->if($field = new outputs\cli(null, null, new prompt()))
			->then
				->object($field->setMethodPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getMethodPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetMethodColorizer()
	{
		$this->assert
			->if($field = new outputs\cli())
			->then
				->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
			->if($field = new outputs\cli(null, null, null, new colorizer()))
			->then
				->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetOutputPrompt()
	{
		$this->assert
			->if($field = new outputs\cli())
			->then
				->object($field->setOutputPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getOutputPrompt())->isIdenticalTo($prompt)
			->if($field = new outputs\cli(null, null, null, null, new prompt()))
			->then
				->object($field->setOutputPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getOutputPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetOutputColorizer()
	{
		$this->assert
			->if($field = new outputs\cli())
			->then
				->object($field->setOutputColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getOutputColorizer())->isIdenticalTo($colorizer)
			->if($field = new outputs\cli(null, null, null, null, null, new colorizer()))
			->then
				->object($field->setOutputColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getOutputColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testHandleEvent()
	{
		$this->assert
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
			->assert
				->if($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getOutputs = array())
				->and($runner = new atoum\runner())
				->and($runner->setScore($score))
				->and($defaultField = new outputs\cli())
				->and($customField = new outputs\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $outputPrompt = new prompt(uniqid()), $outputColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale()))
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
				->and($customField = new outputs\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $outputPrompt = new prompt(uniqid()), $outputColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale()))
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
				->and($customField = new outputs\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $outputPrompt = new prompt(uniqid()), $outputColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale()))
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
