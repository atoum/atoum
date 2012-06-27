<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\php\path;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\tests\units,
	mageekguy\atoum\report\fields\runner
;

require_once __DIR__ . '/../../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\report\fields\runner\php\path')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($field = new runner\php\path\cli())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getPathColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
			->if($field = new runner\php\path\cli(null, null, null, null))
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getPathColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
			->if($field = new runner\php\path\cli($prompt = new prompt(), $titleColorizer = new colorizer(), $pathColorizer = new colorizer(), $locale = new locale()))
			->then
				->object($field->getLocale())->isIdenticalTo($locale)
				->object($field->getPrompt())->isIdenticalTo($prompt)
				->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
				->object($field->getPathColorizer())->isIdenticalTo($pathColorizer)
		;
	}

	public function testSetPrompt()
	{
		$this->assert
			->if($field = new runner\php\path\cli())
			->then
				->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
			->if($field = new runner\php\path\cli(new prompt()))
			->then
				->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$this->assert
			->if($field = new runner\php\path\cli())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
			->if($field = new runner\php\path\cli(null, new colorizer()))
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetPathColorizer()
	{
		$this->assert
			->if($field = new runner\php\path\cli())
			->then
				->object($field->setPathColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getPathColorizer())->isIdenticalTo($colorizer)
			->if($field = new runner\php\path\cli(null, null, new colorizer()))
			->then
				->object($field->setPathColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getPathColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testHandleEvent()
	{
		$this
			->assert
				->if($field = new runner\php\path\cli())
				->and($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getPhpPath = $phpPath = uniqid())
				->then
					->boolean($field->handleEvent(atoum\runner::runStop, $runner = new atoum\runner()))->isFalse()
					->variable($field->getPath())->isNull()
				->if($runner->setScore($score))
					->boolean($field->handleEvent(atoum\runner::runStart, $runner))->isTrue()
					->string($field->getPath())->isEqualTo($phpPath)
		;
	}

	public function test__toString()
	{
		$this
			->assert
				->if($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getPhpPath = $phpPath = uniqid())
				->and($defaultField = new runner\php\path\cli())
				->then
					->castToString($defaultField)->isEqualTo('PHP path: ' . PHP_EOL)
				->if($runner = new atoum\runner())
				->and($runner->setScore($score))
				->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
				->then
					->castToString($defaultField)->isEqualTo('PHP path:' . ' ' . $phpPath . PHP_EOL)
				->if($customField = new runner\php\path\cli($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $pathColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale()))
				->then
					->castToString($customField)->isEqualTo(
						$prompt .
						sprintf(
							$locale->_('%1$s: %2$s'),
							$titleColorizer->colorize($locale->_('PHP path')),
							$pathColorizer->colorize('')
						) .
						PHP_EOL
					)
				->if($customField->handleEvent(atoum\runner::runStart, $runner))
				->then
					->castToString($customField)->isEqualTo(
						$prompt .
						sprintf(
							$locale->_('%1$s: %2$s'),
							$titleColorizer->colorize($locale->_('PHP path')),
							$pathColorizer->colorize($phpPath)
						) .
						PHP_EOL
					)
		;
	}
}
