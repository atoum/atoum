<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\version;

use
	\mageekguy\atoum,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\tests\units,
	\mageekguy\atoum\report\fields\runner\version
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends units\report\fields\runner\version
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('\mageekguy\atoum\report\field')
		;
	}

	public function test__construct()
	{
		$field = new version\cli();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
			->object($field->getPrompt())->isEqualTo(new prompt(version\cli::defaultPrompt))
			->object($field->getColorizer())->isEqualTo(new colorizer('1;36'))
		;

		$field = new version\cli($prompt = new prompt(uniqid()), $colorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getCOlorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetPrompt()
	{
		$field = new version\cli();

		$this->assert
			->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetColorizer()
	{
		$field = new version\cli();

		$this->assert
			->object($field->setColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$score = new atoum\score();
		$score
			->setAtoumPath($atoumPath = uniqid())
			->setAtoumVersion($atoumVersion = uniqid())
		;

		$runner = new atoum\runner();
		$runner->setScore($score);

		$field = new version\cli();

		$this->assert
			->object($field->setWithRunner($runner))->isIdenticalTo($field)
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
			->object($field->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($field)
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
			->object($field->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($field)
			->string($field->getAuthor())->isEqualTo(atoum\author)
			->string($field->getPath())->isEqualTo($atoumPath)
			->string($field->getVersion())->isEqualTo($atoumVersion)
		;
	}

	public function test__toString()
	{
		$score = new atoum\score();
		$score
			->setAtoumPath($atoumPath = uniqid())
			->setAtoumVersion($atoumVersion = uniqid())
		;

		$runner = new atoum\runner();
		$runner->setScore($score);

		$field = new version\cli();

		$this->assert
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getColorizer()->colorize(sprintf($field->getLocale()->_('Atoum version %s by %s (%s)'), $atoumVersion, \mageekguy\atoum\author, $atoumPath)) . PHP_EOL)
		;

		$field = new version\cli($prompt = new prompt(uniqid()), $colorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $colorizer->colorize(sprintf($field->getLocale()->_('Atoum version %s by %s (%s)'), $atoumVersion, \mageekguy\atoum\author, $atoumPath)) . PHP_EOL)
		;
	}
}

?>
