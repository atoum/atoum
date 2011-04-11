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
			->string($field->getLabel())->isEqualTo($field->getLocale()->_('Atoum version %s by %s (%s)'))
		;

		$field = new runner\version\string($locale = new atoum\locale(), $prompt = uniqid(), $label = uniqid());

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getAuthor())->isNull()
			->variable($field->getPath())->isNull()
			->variable($field->getVersion())->isNull()
			->string($field->getPrompt())->isEqualTo($prompt)
			->string($field->getLabel())->isEqualTo($label)
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

	public function testSetLabel()
	{
		$field = new runner\version\string();

		$this->assert
			->object($field->setLabel($label = uniqid()))->isIdenticalTo($field)
			->string($field->getLabel())->isEqualTo($label)
			->object($field->setLabel($label = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getLabel())->isEqualTo((string) $label)
		;
	}

	public function testSetWithRunner()
	{
		$version = new runner\version\string();

		$runner = new atoum\runner();

		$this->assert
			->object($version->setWithRunner($runner))->isIdenticalTo($version)
			->variable($version->getAuthor())->isNull()
			->variable($version->getPath())->isNull()
			->variable($version->getVersion())->isNull()
			->object($version->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($version)
			->variable($version->getAuthor())->isNull()
			->variable($version->getPath())->isNull()
			->variable($version->getVersion())->isNull()
			->object($version->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($version)
			->string($version->getAuthor())->isEqualTo(atoum\author)
			->string($version->getPath())->isEqualTo(realpath(dirname($runner->getPath()) . DIRECTORY_SEPARATOR . '..'))
			->string($version->getVersion())->isEqualTo(atoum\version)
		;
	}

	public function test__toString()
	{
		$version = new runner\version\string();

		$runner = new atoum\runner();

		$this->assert
			->castToString($version->setWithRunner($runner))->isEmpty()
			->castToString($version->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
			->castToString($version->setWithRunner($runner, atoum\runner::runStart))->isEqualTo(runner\version\string::defaultPrompt . sprintf($version->getLocale()->_('Atoum version %s by %s (%s)'), \mageekguy\atoum\version, \mageekguy\atoum\author, \mageekguy\atoum\directory) . PHP_EOL)
		;
	}
}

?>
