<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\version;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends \mageekguy\atoum\tests\units\report\fields\runner\version
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('\mageekguy\atoum\report\field')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(runner\version\cli::defaultPrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\version\cli();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
			->string($field->getPrompt())->isEqualTo(runner\version\cli::defaultPrompt)
		;

		$field = new runner\version\cli($locale = new atoum\locale(), $prompt = uniqid(), $label = uniqid());

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
			->string($field->getPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetPrompt()
	{
		$field = new runner\version\cli();

		$this->assert
			->object($field->setPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo($prompt)
			->object($field->setPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo((string) $prompt)
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

		$field = new runner\version\cli();

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

		$field = new runner\version\cli();

		$this->assert
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Atoum version %s by %s (%s)'), $atoumVersion, \mageekguy\atoum\author, $atoumPath) . PHP_EOL)
		;

		$field = new runner\version\cli($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Atoum version %s by %s (%s)'), $atoumVersion, \mageekguy\atoum\author, $atoumPath) . PHP_EOL)
		;
	}
}

?>
