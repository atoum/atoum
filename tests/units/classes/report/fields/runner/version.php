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
			->string($version->getAuthor())->isEqualTo(atoum\test::author)
			->string($version->getNumber())->isEqualTo(atoum\test::getVersion())
		;
	}

	public function testSetWithRunner()
	{
		$version = new runner\version();

		$this->assert
			->string($version->getAuthor())->isEqualTo(atoum\test::author)
			->string($version->getNumber())->isEqualTo(atoum\test::getVersion())
			->object($version->setWithRunner(new atoum\runner()))->isIdenticalTo($version)
			->string($version->getAuthor())->isEqualTo(atoum\test::author)
			->string($version->getNumber())->isEqualTo(atoum\test::getVersion())
		;
	}

	public function testToString()
	{
		$version = new runner\version();

		$this->assert
			->string($version->toString())->isEqualTo(sprintf($version->getLocale()->_('Atoum version %s by %s.'), $version->getNumber(), $version->getAuthor()));
		;
	}
}

?>
