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
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\php\path');
	}

	public function test__construct()
	{
		$this
			->if($field = new runner\php\path\cli())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getPathColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
		;
	}

	public function testSetPrompt()
	{
		$this
			->if($field = new runner\php\path\cli())
			->then
				->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
				->object($field->setPrompt())->isIdenticalTo($field)
				->object($field->getPrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetTitleColorizer()
	{
		$this
			->if($field = new runner\php\path\cli())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
				->object($field->setTitleColorizer())->isIdenticalTo($field)
				->object($field->getTitleColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetPathColorizer()
	{
		$this
			->if($field = new runner\php\path\cli())
			->then
				->object($field->setPathColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getPathColorizer())->isIdenticalTo($colorizer)
				->object($field->setPathColorizer())->isIdenticalTo($field)
				->object($field->getPathColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new runner\php\path\cli())
			->and($score = new \mock\mageekguy\atoum\runner\score())
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
			->if($score = new \mock\mageekguy\atoum\runner\score())
			->and($score->getMockController()->getPhpPath = $phpPath = uniqid())
			->and($defaultField = new runner\php\path\cli())
			->then
				->castToString($defaultField)->isEqualTo('PHP path: ' . PHP_EOL)
			->if($runner = new atoum\runner())
			->and($runner->setScore($score))
			->and($defaultField->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($defaultField)->isEqualTo('PHP path:' . ' ' . $phpPath . PHP_EOL)
			->if($customField = new runner\php\path\cli())
			->and($customField->setPrompt($prompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setPathColorizer($pathColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new locale()))
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
