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

require_once(__DIR__ . '/../../../../../../runner.php');

class cli extends units\report\fields\runner
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('mageekguy\atoum\report\fields\runner\php\version')
		;
	}

	public function test__construct()
	{
		$field = new runner\php\version\cli();

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getVersionPrompt())->isEqualTo(new prompt())
			->object($field->getVersionColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
		;

		$field = new runner\php\version\cli(null, null, null, null, null);

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getVersionPrompt())->isEqualTo(new prompt())
			->object($field->getVersionColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
		;

		$field = new runner\php\version\cli($titlePrompt = new prompt(), $titleColorizer = new colorizer(), $versionPrompt = new prompt(), $versionColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getVersionPrompt())->isIdenticalTo($versionPrompt)
			->object($field->getVersionColorizer())->isIdenticalTo($versionColorizer)
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new runner\php\version\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getTitlePrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new runner\php\version\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetVersionPrompt()
	{
		$field = new runner\php\version\cli();

		$this->assert
			->object($field->setVersionPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getVersionPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetVersionColorizer()
	{
		$field = new runner\php\version\cli();

		$this->assert
			->object($field->setVersionColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getVersionColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\php\version\cli();

		$this->mock
			->generate('mageekguy\atoum\score')
			->generate('mageekguy\atoum\runner')
		;

		$score = new \mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();
		$scoreController->getPhpVersion = $phpVersion = uniqid();

		$runner = new \mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;

		$this->assert
			->object($field->setWithRunner($runner))->isIdenticalTo($field)
			->string($field->getVersion())->isEqualTo($phpVersion)
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
		$scoreController->getPhpVersion = $phpVersion = uniqid();

		$runner = new \mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;

		$field = new runner\php\version\cli();
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo($field->getLocale()->_('PHP version:') . PHP_EOL
				. $phpVersion . PHP_EOL
			)
		;

		$field = new runner\php\version\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $versionPrompt = new prompt(uniqid()), $versionColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo(
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
		;

		$scoreController->getPhpVersion = ($phpVersionLine1 = uniqid()) . PHP_EOL . ($phpVersionLine2 = uniqid());

		$field = new runner\php\version\cli();
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo(
				'PHP version:' .
				PHP_EOL .
				$phpVersionLine1 .
				PHP_EOL .
				$phpVersionLine2 .
				PHP_EOL
			)
		;

		$field = new runner\php\version\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $versionPrompt = new prompt(uniqid()), $versionColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize($field->getLocale()->_('PHP version'))
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

?>
