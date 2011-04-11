<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\version;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\runner\version
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
			->string(runner\version\string::defaultPrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\version\string();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
			->string($field->getPrompt())->isEqualTo(runner\version\string::defaultPrompt)
		;

		$field = new runner\version\string($locale = new atoum\locale(), $prompt = uniqid(), $label = uniqid());

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
		$field = new runner\version\string();

		$this->assert
			->object($field->setPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo($prompt)
			->object($field->setPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\version\string();

		$runner = new atoum\runner();

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
			->string($field->getPath())->isEqualTo(realpath(dirname($runner->getPath()) . DIRECTORY_SEPARATOR . '..'))
			->string($field->getVersion())->isEqualTo(atoum\version)
		;
	}

	public function test__toString()
	{
		$runner = new atoum\runner();

		$field = new runner\version\string();

		$this->assert
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Atoum version %s by %s (%s)'), \mageekguy\atoum\version, \mageekguy\atoum\author, \mageekguy\atoum\directory) . PHP_EOL)
		;

		$field = new runner\version\string($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Atoum version %s by %s (%s)'), \mageekguy\atoum\version, \mageekguy\atoum\author, \mageekguy\atoum\directory) . PHP_EOL)
		;
	}
}

?>
