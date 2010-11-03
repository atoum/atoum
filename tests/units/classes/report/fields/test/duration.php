<?php

namespace mageekguy\atoum\tests\units\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\test;

require_once(__DIR__ . '/../../../../runner.php');

class duration extends atoum\test
{
	public function test__construct()
	{
		$duration = new test\duration();

		$this->assert
			->object($duration)->isInstanceOf('\mageekguy\atoum\report\fields\test')
			->object($duration->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($duration->getValue())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$duration = new test\duration($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
			->generate('\mageekguy\atoum\score')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); };

		$test = new mock\mageekguy\atoum\test();
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

	public function testToString()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
			->generate('\mageekguy\atoum\score')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); };

		$test = new mock\mageekguy\atoum\test();
		$test->getMockController()->getScore = function() use ($score) { return $score; };

		$duration = new test\duration($locale = new atoum\locale());

		$this->assert
			->string($duration->toString())->isEqualTo($locale->_('Test duration: unknown.') . PHP_EOL)
			->string($duration->setWithTest($test)->toString())->isEqualTo($locale->_('Test duration: unknown.') . PHP_EOL)
			->string($duration->setWithTest($test, atoum\test::runStart)->toString())->isEqualTo($locale->_('Test duration: unknown.') . PHP_EOL)
			->string($duration->setWithTest($test, atoum\test::runStop)->toString())->isEqualTo(sprintf($locale->__('Test duration: %4.2f second.', 'Test duration: %4.2f seconds.', $runningDuration), $runningDuration) . PHP_EOL)
		;
	}
}

?>
