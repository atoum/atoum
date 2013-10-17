<?php

namespace atoum\tests\units\report\fields\runner\atoum;

use
	atoum\locale,
	atoum\runner,
	atoum\runner\score,
	atoum\cli\prompt,
	atoum\cli\colorizer,
	atoum\tests\units,
	atoum\report\fields\runner\atoum
;

require_once __DIR__ . '/../../../../../runner.php';

class phing extends \atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('atoum\report\field');
	}

	public function test__construct()
	{
		$this
			->if($field = new atoum\phing())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getAuthor())->isNull()
				->variable($field->getPath())->isNull()
				->variable($field->getVersion())->isNull()
				->array($field->getEvents())->isEqualTo(array(runner::runStart))
		;
	}

	public function testSetPrompt()
	{
		$this
			->if($field = new atoum\phing())
			->then
				->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
				->object($field->setPrompt())->isIdenticalTo($field)
				->object($field->getPrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetColorizer()
	{
		$this
			->if($field = new atoum\phing())
			->then
				->object($field->setColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getColorizer())->isIdenticalTo($colorizer)
				->object($field->setColorizer())->isIdenticalTo($field)
				->object($field->getColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($score = new score())
			->and($score
				->setAtoumPath($atoumPath = uniqid())
				->setAtoumVersion($atoumVersion = uniqid())
			)
			->and($runner = new runner())
			->and($runner->setScore($score))
			->and($field = new atoum\phing())
			->then
				->variable($field->getAuthor())->isNull()
				->variable($field->getPath())->isNull()
				->variable($field->getVersion())->isNull()
				->boolean($field->handleEvent(runner::runStart, $runner))->isTrue()
				->string($field->getAuthor())->isEqualTo(\atoum\author)
				->string($field->getPath())->isEqualTo($atoumPath)
				->string($field->getVersion())->isEqualTo($atoumVersion)
		;
	}

	public function test__toString()
	{
		$this
			->if($score = new score())
			->and($score
				->setAtoumPath($atoumPath = uniqid())
				->setAtoumVersion($atoumVersion = uniqid())
			)
			->and($runner = new runner())
			->and($runner->setScore($score))
			->and($field = new atoum\phing())
			->and($field->handleEvent(runner::runStop, $runner))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(runner::runStart, $runner))
			->then
				->castToString($field)->isEqualTo($field->getPrompt() . $field->getColorizer()->colorize(sprintf($field->getLocale()->_("Atoum version: %s \nAtoum path: %s \nAtoum author: %s"), $atoumVersion, $atoumPath, \atoum\author)))
		;
	}
}
