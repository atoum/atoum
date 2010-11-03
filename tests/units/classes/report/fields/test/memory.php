<?php

namespace mageekguy\atoum\tests\units\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\test;

require_once(__DIR__ . '/../../../../runner.php');

class memory extends atoum\test
{
	public function test__construct()
	{
		$memory = new test\memory();

		$this->assert
			->object($memory)->isInstanceOf('\mageekguy\atoum\report\fields\test')
			->object($memory->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($memory->getValue())->isNull()
		;
	}

	public function testSetWithTest()
	{
		$memory = new test\memory($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
			->generate('\mageekguy\atoum\score')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(0, PHP_INT_MAX); };

		$test = new mock\mageekguy\atoum\test();
		$test->getMockController()->getScore = function() use ($score) { return $score; };

		$this->assert
			->variable($memory->getValue())->isNull()
			->object($memory->setWithTest($test))->isIdenticalTo($memory)
			->variable($memory->getValue())->isNull()
			->object($memory->setWithTest($test, atoum\test::runStart))->isIdenticalTo($memory)
			->variable($memory->getValue())->isNull()
			->object($memory->setWithTest($test, atoum\test::runStop))->isIdenticalTo($memory)
			->integer($memory->getValue())->isEqualTo($totalMemoryUsage)
		;
	}

	public function testToString()
	{
		$memory = new test\memory($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
			->generate('\mageekguy\atoum\score')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(0, PHP_INT_MAX); };

		$test = new mock\mageekguy\atoum\test();
		$test->getMockController()->getScore = function() use ($score) { return $score; };

		$this->assert
			->string($memory->toString())->isEqualTo($locale->_('Memory usage: unknown.') . PHP_EOL)
			->string($memory->setWithTest($test)->toString())->isEqualTo($locale->_('Memory usage: unknown.') . PHP_EOL)
			->string($memory->setWithTest($test, atoum\test::runStart)->toString())->isEqualTo($locale->_('Memory usage: unknown.') . PHP_EOL)
			->string($memory->setWithTest($test, atoum\test::runStop)->toString())->isEqualTo(sprintf($locale->_('Memory usage: %4.2f Mb.'), $totalMemoryUsage / 1048576) . PHP_EOL)
		;
	}
}

?>
