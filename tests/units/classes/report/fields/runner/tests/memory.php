<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner\tests;

require_once(__DIR__ . '/../../../../../runner.php');

class memory extends atoum\test
{
	public function test__construct()
	{
		$memory = new tests\memory();

		$this->assert
			->object($memory)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($memory->getValue())->isNull()
			->variable($memory->getTestNumber())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$memory = new tests\memory($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(1, PHP_INT_MAX); };

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = function () use ($score) { return $score; };
		$runnerController->getTestNumber = function () use (& $testNumber) { return $testNumber = rand(0, PHP_INT_MAX); };

		$this->assert
			->variable($memory->getValue())->isNull()
			->variable($memory->getTestNumber())->isNull()
			->object($memory->setWithRunner($runner))->isIdenticalTo($memory)
			->variable($memory->getValue())->isNull()
			->variable($memory->getTestNumber())->isNull()
			->object($memory->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($memory)
			->variable($memory->getValue())->isNull()
			->variable($memory->getTestNumber())->isNull()
			->object($memory->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($memory)
			->integer($memory->getValue())->isEqualTo($totalMemoryUsage)
			->integer($memory->getTestNumber())->isEqualTo($testNumber)
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
		$score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(1, PHP_INT_MAX); };

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getTestNumber = function () use (& $testNumber) { return $testNumber = 1; };
		$runnerController->getScore = function () use ($score) { return $score; };

		$memory = new tests\memory($locale = new atoum\locale());

		$this->assert
			->string($memory->toString())->isEqualTo($locale->_('Total test memory usage: unknown.') . PHP_EOL)
			->string($memory->setWithRunner($runner)->toString())->isEqualTo($locale->_('Total test memory usage: unknown.') . PHP_EOL)
			->string($memory->setWithRunner($runner, atoum\runner::runStart)->toString())->isEqualTo($locale->_('Total test memory usage: unknown.') . PHP_EOL)
			->string($memory->setWithRunner($runner, atoum\runner::runStop)->toString())->isEqualTo(sprintf($locale->__('Total test memory usage: %4.2f Mb.', 'Total test memory usage: %4.2f Mb.', $totalMemoryUsage / 1048576), $totalMemoryUsage / 1048576) . PHP_EOL)
		;

		$runnerController->getTestNumber = function () use (& $testNumber) { return $testNumber = rand(2, PHP_INT_MAX); };

		$memory = new tests\memory($locale = new atoum\locale());

		$this->assert
			->string($memory->toString())->isEqualTo($locale->_('Total test memory usage: unknown.') . PHP_EOL)
			->string($memory->setWithRunner($runner)->toString())->isEqualTo($locale->_('Total test memory usage: unknown.') . PHP_EOL)
			->string($memory->setWithRunner($runner, atoum\runner::runStart)->toString())->isEqualTo($locale->_('Total test memory usage: unknown.') . PHP_EOL)
			->string($memory->setWithRunner($runner, atoum\runner::runStop)->toString())->isEqualTo(sprintf($locale->__('Total test memory usage: %4.2f Mb.', 'Total tests memory usage: %4.2f Mb.', $testNumber), $totalMemoryUsage / 1048576) . PHP_EOL)
		;
	}
}

?>
