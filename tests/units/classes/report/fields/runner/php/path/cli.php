<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\php\path;

use
	\mageekguy\atoum,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\tests\units,
	\mageekguy\atoum\report\fields\runner,
	\mageekguy\atoum\mock\mageekguy\atoum as mock
;

require_once(__DIR__ . '/../../../../../../runner.php');

class cli extends units\report\fields\runner
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('\mageekguy\atoum\report\fields\runner\php\path')
		;
	}

	public function test__construct()
	{
		$field = new runner\php\path\cli();

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getPathPrompt())->isEqualTo(new prompt())
			->object($field->getPathColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
		;

		$field = new runner\php\path\cli(null, null, null, null, null);

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getPathPrompt())->isEqualTo(new prompt())
			->object($field->getPathColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
		;

		$field = new runner\php\path\cli($titlePrompt = new prompt(), $titleColorizer = new colorizer(), $pathPrompt = new prompt(), $pathColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getPathPrompt())->isIdenticalTo($pathPrompt)
			->object($field->getPathColorizer())->isIdenticalTo($pathColorizer)
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new runner\php\path\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getTitlePrompt())->isIdenticalTo($prompt)
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

	public function testSetPathPrompt()
	{
		$field = new runner\php\path\cli();

		$this->assert
			->object($field->setPathPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getPathPrompt())->isIdenticalTo($prompt)
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
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\score();
		$scoreController = $score->getMockController();
		$scoreController->getPhpPath = $phpPath = uniqid();

		$runner = new mock\runner();
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
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\score();
		$scoreController = $score->getMockController();
		$scoreController->getPhpPath = $phpPath = uniqid();

		$runner = new mock\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;

		$field = new runner\php\path\cli();
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo($field->getLocale()->_('PHP path:') . ' ' . $phpPath . PHP_EOL)
		;

		$field = new runner\php\path\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $pathPrompt = new prompt(uniqid()), $pathColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo($titlePrompt . $titleColorizer->colorize($locale->_('PHP path:')) . ' ' . $pathColorizer->colorize($phpPath) . PHP_EOL)
		;
	}
}

?>
