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

require_once(__DIR__ . '/../../../../../../runner.php');

class cli extends units\report\fields\runner
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('mageekguy\atoum\report\fields\runner\php\path')
		;
	}

	public function test__construct()
	{
		$field = new runner\php\path\cli();

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getPathColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
		;

		$field = new runner\php\path\cli(null, null, null, null);

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getPathColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
		;

		$field = new runner\php\path\cli($prompt = new prompt(), $titleColorizer = new colorizer(), $pathColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getPathColorizer())->isIdenticalTo($pathColorizer)
		;
	}

	public function testSetPrompt()
	{
		$field = new runner\php\path\cli();

		$this->assert
			->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new runner\php\path\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetPathColorizer()
	{
		$field = new runner\php\path\cli();

		$this->assert
			->object($field->setPathColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getPathColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\php\path\cli();

		$this->mock
			->generate('mageekguy\atoum\score')
			->generate('mageekguy\atoum\runner')
		;

		$score = new \mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();
		$scoreController->getPhpPath = $phpPath = uniqid();

		$runner = new \mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;

		$this->assert
			->object($field->setWithRunner($runner))->isIdenticalTo($field)
			->string($field->getPath())->isEqualTo($phpPath)
		;
	}

	public function test__toString()
	{
		$this->mock
			->generate('mageekguy\atoum\score')
			->generate('mageekguy\atoum\runner')
		;

		$score = new \mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();
		$scoreController->getPhpPath = $phpPath = uniqid();

		$runner = new \mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;

		$field = new runner\php\path\cli();
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo('PHP path:' . ' ' . $phpPath . PHP_EOL)
		;

		$field = new runner\php\path\cli($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $pathColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo(
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

?>
