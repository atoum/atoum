<?php

namespace mageekguy\atoum\tests\units\report\fields\test\memory;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\test;

require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\test\memory
{
	public function testClassConstants()
	{
		$this->assert
			->string(test\memory\string::titlePrompt)->isEqualTo('=> ')
		;
	}

	public function test__construct()
	{
		$memory = new test\memory\string();

		$this->assert
			->object($memory)->isInstanceOf('\mageekguy\atoum\report\fields\test')
			->object($memory->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($memory->getValue())->isNull()
		;
	}

	public function testSetWithTest()
	{
		$memory = new test\memory\string($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
			->generate('\mageekguy\atoum\score')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(0, PHP_INT_MAX); };

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);
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

	public function test__toString()
	{
		$memory = new test\memory\string($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
			->generate('\mageekguy\atoum\score')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(0, PHP_INT_MAX); };

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);
		$test->getMockController()->getScore = function() use ($score) { return $score; };

		$this->assert
			->castToString($memory)->isEqualTo(test\memory\string::titlePrompt . $locale->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($memory->setWithTest($test))->isEqualTo(test\memory\string::titlePrompt . $locale->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($memory->setWithTest($test, atoum\test::runStart))->isEqualTo(test\memory\string::titlePrompt . $locale->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($memory->setWithTest($test, atoum\test::runStop))->isEqualTo(test\memory\string::titlePrompt . sprintf($locale->_('Memory usage: %4.2f Mb.'), $totalMemoryUsage / 1048576) . PHP_EOL)
		;
	}
}

?>
