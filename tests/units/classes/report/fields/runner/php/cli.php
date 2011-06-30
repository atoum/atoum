<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\php;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends \mageekguy\atoum\tests\units\report\fields\runner
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('\mageekguy\atoum\report\fields\runner')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(runner\php\cli::defaultTitlePrompt)->isEqualTo('> ')
			->string(runner\php\cli::defaultVersionPrompt)->isEqualTo('=> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\php\cli();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->string($field->getTitlePrompt())->isEqualTo(runner\php\cli::defaultTitlePrompt)
			->string($field->getVersionPrompt())->isEqualTo(runner\php\cli::defaultVersionPrompt)
		;

		$field = new runner\php\cli($locale = new atoum\locale(), $titlePrompt = uniqid(), $versionPrompt = uniqid());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->string($field->getTitlePrompt())->isEqualTo($titlePrompt)
			->string($field->getVersionPrompt())->isEqualTo($versionPrompt)
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new runner\php\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo($prompt)
			->object($field->setTitlePrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetVersionPrompt()
	{
		$field = new runner\php\cli();

		$this->assert
			->object($field->setVersionPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getVersionPrompt())->isEqualTo($prompt)
			->object($field->setVersionPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getVersionPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\php\cli();

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();
		$scoreController->getPhpPath = $phpPath = uniqid();
		$scoreController->getPhpVersion = $phpVersion = uniqid();

		$runner = new mock\mageekguy\atoum\runner();
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
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();
		$scoreController->getPhpPath = $phpPath = uniqid();
		$scoreController->getPhpVersion = $phpVersion = uniqid();

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;

		$field = new runner\php\cli();
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->_('PHP path: %s'), $phpPath) . PHP_EOL
				. $field->getTitlePrompt() . $field->getLocale()->_('PHP version:') . PHP_EOL
				. $field->getVersionPrompt() . $phpVersion . PHP_EOL
			)
		;

		$field = new runner\php\cli($locale = new atoum\locale(), $titlePrompt = uniqid(), $versionPrompt = uniqid());
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->_('PHP path: %s'), $phpPath) . PHP_EOL
				. $field->getTitlePrompt() . $field->getLocale()->_('PHP version:') . PHP_EOL
				. $field->getVersionPrompt() . $phpVersion . PHP_EOL
			)
		;

		$scoreController->getPhpVersion = ($phpVersionLine1 = uniqid()) . PHP_EOL . ($phpVersionLine2 = uniqid());

		$field = new runner\php\cli();
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->_('PHP path: %s'), $phpPath) . PHP_EOL
				. $field->getTitlePrompt() . $field->getLocale()->_('PHP version:') . PHP_EOL
				. $field->getVersionPrompt() . $phpVersionLine1 . PHP_EOL
				. $field->getVersionPrompt() . $phpVersionLine2 . PHP_EOL
			)
		;

		$field = new runner\php\cli($locale = new atoum\locale(), $titlePrompt = uniqid(), $versionPrompt = uniqid());
		$field->setWithRunner($runner);

		$this->assert
			->castToString($field)->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->_('PHP path: %s'), $phpPath) . PHP_EOL
				. $field->getTitlePrompt() . $field->getLocale()->_('PHP version:') . PHP_EOL
				. $field->getVersionPrompt() . $phpVersionLine1 . PHP_EOL
				. $field->getVersionPrompt() . $phpVersionLine2 . PHP_EOL
			)
		;
	}
}

?>
