<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner\tests;

require_once(__DIR__ . '/../../../../../runner.php');

class duration extends atoum\test
{
	public function test__construct()
	{
		$duration = new tests\duration();

		$this->assert
			->object($duration)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($duration->getValue())->isNull()
			->variable($duration->getTestsNumber())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$duration = new tests\duration($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$totalDuration = rand(1, PHP_INT_MAX);

		$score = new mock\mageekguy\atoum\score();
		$score
			->getMockController()
				->getTotalDuration = function() use ($totalDuration) { return $totalDuration; }
		;

		$testsNumber = rand(0, PHP_INT_MAX);

		$runner = new mock\mageekguy\atoum\runner();

		$runnerController = $runner->getMockController();
		$runnerController->getTestsNumber = function () use ($testsNumber) { return $testsNumber; };
		$runnerController->getScore = function () use ($score) { return $score; };

		$this->assert
			->variable($duration->getValue())->isNull()
			->variable($duration->getTestsNumber())->isNull()
			->object($duration->setWithRunner($runner))->isIdenticalTo($duration)
			->integer($duration->getValue())->isEqualTo($totalDuration)
			->integer($duration->getTestsNumber())->isEqualTo($testsNumber)
		;
	}

	public function testToString()
	{
		$duration = new tests\duration($locale = new atoum\locale());

		$this->assert
			->string($duration->toString())->isEqualTo($locale->_('Total test duration: unknown.'))
		;

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$testsNumber = 1;

		$runner = new mock\mageekguy\atoum\runner();

		$runnerController = $runner->getMockController();
		$runnerController->getTestsNumber = function () use ($testsNumber) { return $testsNumber; };

		$totalDuration = 0.5;

		$score = new mock\mageekguy\atoum\score();
		$score
			->getMockController()
				->getTotalDuration = function() use ($totalDuration) { return $totalDuration; }
		;

		$runnerController->getScore = function () use ($score) { return $score; };

		$duration->setWithRunner($runner);

		$this->assert
			->string($duration->toString())->isEqualTo(sprintf($locale->__('Total test duration: %4.2f second.', 'Total test duration: %4.2f seconds.', $totalDuration), $totalDuration))
		;

		$totalDuration = rand(2, PHP_INT_MAX);

		$score
			->getMockController()
				->getTotalDuration = function() use ($totalDuration) { return $totalDuration; }
		;

		$duration->setWithRunner($runner);

		$this->assert
			->string($duration->toString())->isEqualTo(sprintf($locale->__('Total test duration: %4.2f second.', 'Total test duration: %4.2f seconds.', $totalDuration), $totalDuration))
		;

		$testsNumber = rand(2, PHP_INT_MAX);

		$runnerController->getTestsNumber = function () use ($testsNumber) { return $testsNumber; };

		$duration->setWithRunner($runner);

		$this->assert
			->string($duration->toString())->isEqualTo(sprintf($locale->__('Total tests duration: %4.2f second.', 'Total tests duration: %4.2f seconds.', $totalDuration), $totalDuration))
		;
	}

}
?>
