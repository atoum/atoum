<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\php;

use
	\mageekguy\atoum,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\tests\units,
	\mageekguy\atoum\report\fields\runner,
	\mageekguy\atoum\mock\mageekguy\atoum as mock
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends units\report\fields\runner
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('\mageekguy\atoum\report\fields\runner')
		;
	}

	public function test__construct()
	{
		$field = new runner\php\cli();

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getDataPrompt())->isEqualTo(new prompt())
			->object($field->getDataColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
		;

		$field = new runner\php\cli(null, null, null, null, null);

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getDataPrompt())->isEqualTo(new prompt())
			->object($field->getDataColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
		;

		$field = new runner\php\cli($titlePrompt = new prompt(), $titleColorizer = new colorizer(), $dataPrompt = new prompt(), $dataColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getDataPrompt())->isIdenticalTo($dataPrompt)
			->object($field->getDataColorizer())->isIdenticalTo($dataColorizer)
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new runner\php\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getTitlePrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new runner\php\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetDataPrompt()
	{
		$field = new runner\php\cli();

		$this->assert
			->object($field->setDataPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getDataPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetDataColorizer()
	{
		$field = new runner\php\cli();

		$this->assert
			->object($field->setDataColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getDataColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\php\cli();

		$this->mock
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\score();
		$scoreController = $score->getMockController();
		$scoreController->getPhpPath = $phpPath = uniqid();
		$scoreController->getPhpVersion = $phpVersion = uniqid();

		$runner = new mock\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;

		$this->assert
			->object($field->setWithRunner($runner))->isIdenticalTo($field)
			->string($field->getPhpPath())->isEqualTo($phpPath)
			->string($field->getPhpVersion())->isEqualTo($phpVersion)
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
		$scoreController->getPhpVersion = $phpVersion = uniqid();

		$runner = new mock\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;

		$field = new runner\php\cli();
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo(
				  $field->getLocale()->_('PHP path:') . ' ' . $phpPath . PHP_EOL
				. $field->getLocale()->_('PHP version:') . PHP_EOL
				. $phpVersion . PHP_EOL
			)
		;

		$field = new runner\php\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $dataPrompt = new prompt(uniqid()), $dataColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo($titlePrompt . $titleColorizer->colorize($locale->_('PHP path:')) . ' ' . $dataColorizer->colorize($phpPath) . PHP_EOL
				. $titlePrompt . $titleColorizer->colorize($locale->_('PHP version:')) . PHP_EOL
				. $dataPrompt . $dataColorizer->colorize($phpVersion) . PHP_EOL
			)
		;

		$scoreController->getPhpVersion = ($phpVersionLine1 = uniqid()) . PHP_EOL . ($phpVersionLine2 = uniqid());

		$field = new runner\php\cli();
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo($field->getLocale()->_('PHP path:') . ' ' . $phpPath . PHP_EOL
				. $field->getLocale()->_('PHP version:') . PHP_EOL
				. $phpVersionLine1 . PHP_EOL
				. $phpVersionLine2 . PHP_EOL
			)
		;

		$field = new runner\php\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $dataPrompt = new prompt(uniqid()), $dataColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo($titlePrompt . $titleColorizer->colorize($field->getLocale()->_('PHP path:')) . ' ' . $dataColorizer->colorize($phpPath) . PHP_EOL
				. $titlePrompt . $titleColorizer->colorize($field->getLocale()->_('PHP version:')) . PHP_EOL
				. $dataPrompt . $dataColorizer->colorize($phpVersionLine1) . PHP_EOL
				. $dataPrompt . $dataColorizer->colorize($phpVersionLine2) . PHP_EOL
			)
		;
	}
}

?>
