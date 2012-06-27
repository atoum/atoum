<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\atoum;

use
	mageekguy\atoum\score,
	mageekguy\atoum\runner,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\tests\units,
	mageekguy\atoum\report\fields\runner\atoum
;

require_once __DIR__ . '/../../../../../runner.php';

class cli extends \mageekguy\atoum\test
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('mageekguy\atoum\report\field')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($field = new atoum\cli())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getAuthor())->isNull()
				->variable($field->getPath())->isNull()
				->variable($field->getVersion())->isNull()
				->array($field->getEvents())->isEqualTo(array(runner::runStart))
			->if($field = new atoum\cli($prompt = new prompt(), $colorizer = new colorizer(), $locale = new locale()))
			->then
				->object($field->getPrompt())->isIdenticalTo($prompt)
				->object($field->getColorizer())->isIdenticalTo($colorizer)
				->object($field->getLocale())->isIdenticalTo($locale)
				->variable($field->getAuthor())->isNull()
				->variable($field->getPath())->isNull()
				->variable($field->getVersion())->isNull()
				->array($field->getEvents())->isEqualTo(array(runner::runStart))
		;
	}

	public function testSetPrompt()
	{
		$field = new atoum\cli();

		$this->assert
			->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetColorizer()
	{
		$field = new atoum\cli();

		$this->assert
			->object($field->setColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testHandleEvent()
	{
		$score = new score();
		$score
			->setAtoumPath($atoumPath = uniqid())
			->setAtoumVersion($atoumVersion = uniqid())
		;

		$runner = new runner();
		$runner->setScore($score);

		$field = new atoum\cli();

		$this->assert
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
			->boolean($field->handleEvent(runner::runStart, $runner))->isTrue()
			->string($field->getAuthor())->isEqualTo(\mageekguy\atoum\author)
			->string($field->getPath())->isEqualTo($atoumPath)
			->string($field->getVersion())->isEqualTo($atoumVersion)
		;
	}

	public function test__toString()
	{
		$score = new score();
		$score
			->setAtoumPath($atoumPath = uniqid())
			->setAtoumVersion($atoumVersion = uniqid())
		;

		$runner = new runner();
		$runner->setScore($score);

		$this->assert
			->if($field = new atoum\cli())
			->and($field->handleEvent(runner::runStop, $runner))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(runner::runStart, $runner))
			->then
				->castToString($field)->isEqualTo($field->getPrompt() . $field->getColorizer()->colorize(sprintf($field->getLocale()->_('atoum version %s by %s (%s)'), $atoumVersion, \mageekguy\atoum\author, $atoumPath)) . PHP_EOL)
			->if($field = new atoum\cli($prompt = new prompt(uniqid()), $colorizer = new colorizer(uniqid(), uniqid()), null, $locale = new locale()))
			->and($field->handleEvent(runner::runStop, $runner))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(runner::runStart, $runner))
			->then
				->castToString($field)->isEqualTo($field->getPrompt() . $colorizer->colorize(sprintf($field->getLocale()->_('atoum version %s by %s (%s)'), $atoumVersion, \mageekguy\atoum\author, $atoumPath)) . PHP_EOL)
		;
	}
}
