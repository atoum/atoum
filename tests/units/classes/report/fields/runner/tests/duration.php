<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner\tests;

require_once(__DIR__ . '/../../../../../runner.php');

class duration extends atoum\test
{
	public function testClassConstant()
	{
		$this->assert
			->string(tests\duration::titlePrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$duration = new tests\duration();

		$this->assert
			->object($duration)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($duration->getValue())->isNull()
			->variable($duration->getTestNumber())->isNull()
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

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalDuration = function() use (& $totalDuration) { return $totalDuration = rand(1, PHP_INT_MAX); };

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getTestNumber = function () use (& $testsNumber) { return $testsNumber = rand(1, PHP_INT_MAX); };
		$runnerController->getScore = function () use ($score) { return $score; };

		$this->assert
			->variable($duration->getValue())->isNull()
			->variable($duration->getTestNumber())->isNull()
			->object($duration->setWithRunner($runner))->isIdenticalTo($duration)
			->variable($duration->getValue())->isNull()
			->variable($duration->getTestNumber())->isNull()
			->object($duration->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($duration)
			->variable($duration->getValue())->isNull()
			->variable($duration->getTestNumber())->isNull()
			->object($duration->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($duration)
			->integer($duration->getValue())->isEqualTo($totalDuration)
			->integer($duration->getTestNumber())->isEqualTo($testsNumber)
		;
	}

	public function testToString()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalDuration = function() use (& $totalDuration) { return $totalDuration = (rand(1, 100) / 1000); };

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getTestNumber = function () use (& $testNumber) { return $testNumber = 1; };
		$runnerController->getScore = function () use ($score) { return $score; };

		$duration = new tests\duration($locale = new atoum\locale());

		$this->assert
			->string($duration->toString())->isEqualTo(tests\duration::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->string($duration->setWithRunner($runner)->toString())->isEqualTo(tests\duration::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->string($duration->setWithRunner($runner, atoum\runner::runStart)->toString())->isEqualTo(tests\duration::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->string($duration->setWithRunner($runner, atoum\runner::runStop)->toString())->isEqualTo(tests\duration::titlePrompt . sprintf($locale->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($locale->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$runnerController->getTestNumber = function () use (& $testNumber) { return $testNumber = rand(2, PHP_INT_MAX); };

		$duration = new tests\duration($locale = new atoum\locale());

		$this->assert
			->string($duration->toString())->isEqualTo(tests\duration::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->string($duration->setWithRunner($runner)->toString())->isEqualTo(tests\duration::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->string($duration->setWithRunner($runner, atoum\runner::runStart)->toString())->isEqualTo(tests\duration::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->string($duration->setWithRunner($runner, atoum\runner::runStop)->toString())->isEqualTo(tests\duration::titlePrompt . sprintf($locale->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($locale->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$score->getMockController()->getTotalDuration = function() use (& $totalDuration) { return $totalDuration = rand(2, PHP_INT_MAX); };

		$duration = new tests\duration($locale = new atoum\locale());

		$this->assert
			->string($duration->toString())->isEqualTo($locale->_(tests\duration::titlePrompt . 'Total test duration: unknown.') . PHP_EOL)
			->string($duration->setWithRunner($runner)->toString())->isEqualTo(tests\duration::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->string($duration->setWithRunner($runner, atoum\runner::runStart)->toString())->isEqualTo(tests\duration::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->string($duration->setWithRunner($runner, atoum\runner::runStop)->toString())->isEqualTo(tests\duration::titlePrompt . sprintf($locale->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($locale->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;
	}
}

?>
