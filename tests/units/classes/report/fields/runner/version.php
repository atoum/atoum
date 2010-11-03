<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../runner.php');

class version extends atoum\test
{
	public function test__construct()
	{
		$version = new runner\version();

		$this->assert
			->object($version)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($version->getAuthor())->isNull()
			->variable($version->getNumber())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$version = new runner\version();

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

	public function testToString()
	{
		$version = new runner\version();

		$runner = new atoum\runner();

		$this->assert
			->string($version->setWithRunner($runner)->toString())->isEmpty()
			->string($version->setWithRunner($runner, atoum\runner::runStop)->toString())->isEmpty()
			->string($version->setWithRunner($runner, atoum\runner::runStart)->toString())->isEqualTo(sprintf($version->getLocale()->_('Atoum version %s by %s.'), $version->getNumber(), $version->getAuthor()) . PHP_EOL)
		;
	}
}

?>
