<?php

namespace mageekguy\atoum\tests\units\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\test;

require_once(__DIR__ . '/../../../../runner.php');

class run extends atoum\test
{
	public function test__construct()
	{
		$run = new test\run();

		$this->assert
			->object($run)->isInstanceOf('\mageekguy\atoum\report\fields\test')
			->variable($run->getTestClass())->isNull()
		;
	}

	public function testSetWithTest()
	{
		$run = new test\run();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$test = new mock\mageekguy\atoum\test();

		$this->assert
			->object($run->setWithTest($test))->isIdenticalTo($run)
			->string($run->getTestClass())->isEqualTo($test->getClass())
		;
	}

	public function testToString()
	{
		$run = new test\run($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$test = new mock\mageekguy\atoum\test();

		$this->assert
			->string($run->toString())->isEqualTo($locale->_('There is currently no test running.'))
		;

		$run->setWithTest($test);

		$this->assert
			->string($run->toString())->isEqualTo(sprintf($locale->_('Run %s...'), $test->getClass()))
		;
	}
}

?>
