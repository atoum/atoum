<?php

namespace mageekguy\atoum\tests\units\report\fields\test\duration;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\test;

require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\test\duration
{
	public function testClassConstants()
	{
		$this->assert
			->string(test\duration\string::titlePrompt)->isEqualTo('=> ')
		;
	}

	public function test__construct()
	{
		$duration = new test\duration\string();

		$this->assert
			->object($duration)->isInstanceOf('\mageekguy\atoum\report\fields\test')
			->object($duration->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($duration->getValue())->isNull()
		;
	}

	public function testSetWithTest()
	{
		$duration = new test\duration\string($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
			->generate('\mageekguy\atoum\score')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); };

		$adapter = new atoum\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);
		$test->getMockController()->getScore = function() use ($score) { return $score; };

		$this->assert
			->variable($duration->getValue())->isNull()
			->object($duration->setWithTest($test))->isIdenticalTo($duration)
			->variable($duration->getValue())->isNull()
			->object($duration->setWithTest($test, atoum\test::runStart))->isIdenticalTo($duration)
			->variable($duration->getValue())->isNull()
			->object($duration->setWithTest($test, atoum\test::runStop))->isIdenticalTo($duration)
			->integer($duration->getValue())->isEqualTo($runningDuration)
		;
	}

	public function test__toString()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
			->generate('\mageekguy\atoum\score')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); };

		$adapter = new atoum\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);
		$test->getMockController()->getScore = function() use ($score) { return $score; };

		$duration = new test\duration\string($locale = new atoum\locale());

		$this->assert
			->castToString($duration)->isEqualTo(test\duration\string::titlePrompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test))->isEqualTo(test\duration\string::titlePrompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test, atoum\test::runStart))->isEqualTo(test\duration\string::titlePrompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test, atoum\test::runStop))->isEqualTo(test\duration\string::titlePrompt . sprintf($locale->__('Test duration: %4.2f second.', 'Test duration: %4.2f seconds.', $runningDuration), $runningDuration) . PHP_EOL)
		;
	}
}

?>
