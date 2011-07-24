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

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends units\report\fields\runner
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('mageekguy\atoum\report\field')
		;
	}

	public function test__construct()
	{
		$field = new atoum\cli();

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
		;

		$field = new atoum\cli(null, null, null);

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
		;


		$field = new atoum\cli($prompt = new prompt(), $colorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getColorizer())->isIdenticalTo($colorizer)
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
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

	public function testSetWithRunner()
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
			->object($field->setWithRunner($runner))->isIdenticalTo($field)
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
			->object($field->setWithRunner($runner, runner::runStop))->isIdenticalTo($field)
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
			->object($field->setWithRunner($runner, runner::runStart))->isIdenticalTo($field)
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

		$field = new atoum\cli();

		$this->assert
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, runner::runStop))->isEmpty()
			->castToString($field->setWithRunner($runner, runner::runStart))->isEqualTo($field->getPrompt() . $field->getColorizer()->colorize(sprintf($field->getLocale()->_('atoum version %s by %s (%s)'), $atoumVersion, \mageekguy\atoum\author, $atoumPath)) . PHP_EOL)
		;

		$field = new atoum\cli($prompt = new prompt(uniqid()), $colorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, runner::runStop))->isEmpty()
			->castToString($field->setWithRunner($runner, runner::runStart))->isEqualTo($field->getPrompt() . $colorizer->colorize(sprintf($field->getLocale()->_('atoum version %s by %s (%s)'), $atoumVersion, \mageekguy\atoum\author, $atoumPath)) . PHP_EOL)
		;
	}
}

?>
