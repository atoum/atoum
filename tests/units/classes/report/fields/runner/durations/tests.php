<?php

namespace mageekguy\atoum\tests\units\report\fields\durations\runnings;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner\durations;

require_once(__DIR__ . '/../../../../../runner.php');

class tests extends atoum\test
{
	public function test__construct()
	{
		$tests = new durations\tests();

		$this->assert
			->object($tests)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($tests->getDuration())->isNull()
			->variable($tests->getTestsNumber())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$tests = new durations\tests($locale = new atoum\locale());

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
			->variable($tests->getDuration())->isNull()
			->variable($tests->getTestsNumber())->isNull()
			->object($tests->setWithRunner($runner))->isIdenticalTo($tests)
			->integer($tests->getDuration())->isEqualTo($totalDuration)
			->integer($tests->getTestsNumber())->isEqualTo($testsNumber)
		;
	}

	public function testToString()
	{
		$tests = new durations\tests($locale = new atoum\locale());

		$this->assert
			->string($tests->toString())->isEqualTo($locale->_('Total test duration: unknown.'))
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

		$tests->setWithRunner($runner);

		$this->assert
			->string($tests->toString())->isEqualTo(sprintf($locale->__('Total test duration: %4.2f second.', 'Total test duration: %4.2f seconds.', $totalDuration), $totalDuration))
		;

		$totalDuration = rand(1, PHP_INT_MAX);

		$score
			->getMockController()
				->getTotalDuration = function() use ($totalDuration) { return $totalDuration; }
		;

		$tests->setWithRunner($runner);

		$this->assert
			->string($tests->toString())->isEqualTo(sprintf($locale->__('Total test duration: %4.2f second.', 'Total test duration: %4.2f seconds.', $totalDuration), $totalDuration))
		;

		$testsNumber = rand(2, PHP_INT_MAX);

		$runnerController->getTestsNumber = function () use ($testsNumber) { return $testsNumber; };

		$tests->setWithRunner($runner);

		$this->assert
			->string($tests->toString())->isEqualTo(sprintf($locale->__('Total tests duration: %4.2f second.', 'Total tests duration: %4.2f seconds.', $totalDuration), $totalDuration))
		;
	}
}

?>
