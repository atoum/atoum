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
	public function testClassConstants()
	{
		$this->assert
			->string(runner\version\string::titlePrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$version = new runner\version\string();

		$this->assert
			->object($version)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($version->getAuthor())->isNull()
			->variable($version->getNumber())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$version = new runner\version\string();

		$runner = new atoum\runner();

		$this->assert
			->object($version->setWithRunner($runner))->isIdenticalTo($version)
			->variable($version->getAuthor())->isNull()
			->variable($version->getNumber())->isNull()
			->object($version->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($version)
			->variable($version->getAuthor())->isNull()
			->variable($version->getNumber())->isNull()
			->object($version->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($version)
			->string($version->getAuthor())->isEqualTo(atoum\test::author)
			->string($version->getNumber())->isEqualTo(atoum\test::getVersion())
		;
	}

	public function test__toString()
	{
		$version = new runner\version\string();

		$runner = new atoum\runner();

		$this->assert
			->castToString($version->setWithRunner($runner))->isEmpty()
			->castToString($version->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
			->castToString($version->setWithRunner($runner, atoum\runner::runStart))->isEqualTo(runner\version\string::titlePrompt . sprintf($version->getLocale()->_('Atoum version %s by %s.'), $version->getNumber(), $version->getAuthor()) . PHP_EOL)
		;
	}
}

?>
