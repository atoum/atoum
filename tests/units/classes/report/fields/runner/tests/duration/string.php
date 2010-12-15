<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\duration;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner\tests;
require_once(__DIR__ . '/../duration.php');
require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\runner\tests\duration
{
	public function testClassConstant()
	{
		$this->assert
			->string(tests\duration\string::titlePrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$duration = new tests\duration\string();

		$this->assert
			->object($duration)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($duration->getValue())->isNull()
			->variable($duration->getTestNumber())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$duration = new tests\duration\string($locale = new atoum\locale());

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

	public function test__toString()
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

		$duration = new tests\duration\string($locale = new atoum\locale());

		$this->assert
			->castToString($duration)->isEqualTo(tests\duration\string::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner))->isEqualTo(tests\duration\string::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner, atoum\runner::runStart))->isEqualTo(tests\duration\string::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner, atoum\runner::runStop))->isEqualTo(tests\duration\string::titlePrompt . sprintf($locale->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($locale->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$runnerController->getTestNumber = function () use (& $testNumber) { return $testNumber = rand(2, PHP_INT_MAX); };

		$duration = new tests\duration\string($locale = new atoum\locale());

		$this->assert
			->castToString($duration)->isEqualTo(tests\duration\string::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner))->isEqualTo(tests\duration\string::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner, atoum\runner::runStart))->isEqualTo(tests\duration\string::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner, atoum\runner::runStop))->isEqualTo(tests\duration\string::titlePrompt . sprintf($locale->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($locale->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$score->getMockController()->getTotalDuration = function() use (& $totalDuration) { return $totalDuration = rand(2, PHP_INT_MAX); };

		$duration = new tests\duration\string($locale = new atoum\locale());

		$this->assert
			->castToString($duration)->isEqualTo($locale->_(tests\duration\string::titlePrompt . 'Total test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner))->isEqualTo(tests\duration\string::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner, atoum\runner::runStart))->isEqualTo(tests\duration\string::titlePrompt . $locale->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner, atoum\runner::runStop))->isEqualTo(tests\duration\string::titlePrompt . sprintf($locale->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($locale->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;
	}
}

?>
