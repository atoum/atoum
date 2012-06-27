<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\php\version;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\tests\units,
	mageekguy\atoum\report\fields\runner,
	mageekguy\atoum\mock\mageekguy\atoum as mock
;

require_once __DIR__ . '/../../../../../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\report\fields\runner\php\version')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($field = new runner\php\version\cli())
			->then
				->object($field->getTitlePrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getVersionPrompt())->isEqualTo(new prompt())
				->object($field->getVersionColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStart))
			->if($field = new runner\php\version\cli(null, null, null, null, null))
			->then
				->object($field->getTitlePrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getVersionPrompt())->isEqualTo(new prompt())
				->object($field->getVersionColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStart))
			->if($field = new runner\php\version\cli($titlePrompt = new prompt(), $titleColorizer = new colorizer(), $versionPrompt = new prompt(), $versionColorizer = new colorizer(), $locale = new locale()))
			->then
				->object($field->getLocale())->isIdenticalTo($locale)
				->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
				->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
				->object($field->getVersionPrompt())->isIdenticalTo($versionPrompt)
				->object($field->getVersionColorizer())->isIdenticalTo($versionColorizer)
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStart))
		;
	}

	public function testSetTitlePrompt()
	{
		$this->assert
			->if($field = new runner\php\version\cli())
			->then
				->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isIdenticalTo($prompt)
			->if($field = new runner\php\version\cli(new prompt()))
			->then
				->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$this->assert
			->if($field = new runner\php\version\cli())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
			->if($field = new runner\php\version\cli(null, new colorizer()))
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetVersionPrompt()
	{
		$this->assert
			->if($field = new runner\php\version\cli())
			->then
				->object($field->setVersionPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getVersionPrompt())->isIdenticalTo($prompt)
			->if($field = new runner\php\version\cli(null, null, new prompt()))
			->then
				->object($field->setVersionPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getVersionPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetVersionColorizer()
	{
		$this->assert
			->if($field = new runner\php\version\cli())
			->then
				->object($field->setVersionColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getVersionColorizer())->isIdenticalTo($colorizer)
			->if($field = new runner\php\version\cli(null, null, null, new colorizer()))
			->then
				->object($field->setVersionColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getVersionColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testHandleEvent()
	{

		$this
			->assert
				->if($field = new runner\php\version\cli())
				->and($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getPhpVersion = $phpVersion = uniqid())
				->and($runner = new atoum\runner())
				->and($runner->setScore($score))
				->then
					->boolean($field->handleEvent(atoum\runner::runStop, $runner))->isFalse()
					->variable($field->getVersion())->isNull()
					->boolean($field->handleEvent(atoum\runner::runStart, $runner))->isTrue()
					->string($field->getVersion())->isEqualTo($phpVersion)
		;
	}

	public function test__toString()
	{
		$this
			->assert
				->if($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getPhpVersion = $phpVersion = uniqid())
				->and($runner = new atoum\runner())
				->and($runner->setScore($score))
				->and($defaultField = new runner\php\version\cli())
				->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
				->then
					->castToString($defaultField)->isEqualTo(
							$defaultField->getLocale()->_('PHP version:') .
							PHP_EOL .
							$phpVersion .
							PHP_EOL
						)
				->if($customField = new runner\php\version\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $versionPrompt = new prompt(uniqid()), $versionColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale()))
				->and($customField->handleEvent(atoum\runner::runStart, $runner))
				->then
					->castToString($customField)->isEqualTo(
						$titlePrompt .
						sprintf(
							$locale->_('%s:'),
							$titleColorizer->colorize($locale->_('PHP version'))
						) .
						PHP_EOL .
						$versionPrompt .
						$versionColorizer->colorize($phpVersion) .
						PHP_EOL
					)
				->if($score->getMockController()->getPhpVersion = ($phpVersionLine1 = uniqid()) . PHP_EOL . ($phpVersionLine2 = uniqid()))
				->and($defaultField = new runner\php\version\cli())
				->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
				->then
					->castToString($defaultField)->isEqualTo(
						'PHP version:' .
						PHP_EOL .
						$phpVersionLine1 .
						PHP_EOL .
						$phpVersionLine2 .
						PHP_EOL
					)
				->if($customField = new runner\php\version\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $versionPrompt = new prompt(uniqid()), $versionColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale()))
				->and($customField->handleEvent(atoum\runner::runStart, $runner))
				->then
					->castToString($customField)->isEqualTo(
						$titlePrompt .
						sprintf(
							$locale->_('%s:'),
							$titleColorizer->colorize($locale->_('PHP version'))
						) .
						PHP_EOL .
						$versionPrompt .
						$versionColorizer->colorize($phpVersionLine1) .
						PHP_EOL .
						$versionPrompt .
						$versionColorizer->colorize($phpVersionLine2) .
						PHP_EOL
					)
		;
	}
}
