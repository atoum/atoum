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
			->variable($memory->getTestsNumber())->isNull()
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

		$totalMemoryUsage = rand(1, PHP_INT_MAX);

		$score = new mock\mageekguy\atoum\score();
		$score
			->getMockController()
				->getTotalMemoryUsage = function() use ($totalMemoryUsage) { return $totalMemoryUsage; }
		;

		$testsNumber = rand(0, PHP_INT_MAX);

		$runner = new mock\mageekguy\atoum\runner();

		$runnerController = $runner->getMockController();
		$runnerController->getTestsNumber = function () use ($testsNumber) { return $testsNumber; };
		$runnerController->getScore = function () use ($score) { return $score; };

		$this->assert
			->variable($memory->getValue())->isNull()
			->variable($memory->getTestsNumber())->isNull()
			->object($memory->setWithRunner($runner))->isIdenticalTo($memory)
			->integer($memory->getValue())->isEqualTo($totalMemoryUsage)
			->integer($memory->getTestsNumber())->isEqualTo($testsNumber)
		;
	}

	public function testToString()
	{
		$memory = new tests\memory($locale = new atoum\locale());

		$this->assert
			->string($memory->toString())->isEqualTo($locale->_('Total test memory usage: unknown.'))
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

		$totalMemoryUsage = 0.5;

		$score = new mock\mageekguy\atoum\score();
		$score
			->getMockController()
				->getTotalDuration = function() use ($totalMemoryUsage) { return $totalMemoryUsage; }
		;

		$runnerController->getScore = function () use ($score) { return $score; };

		$memory->setWithRunner($runner);

		$this->assert
			->string($memory->toString())->isEqualTo(sprintf($locale->__('Total test memory usage: %4.2f Mb.', 'Total test memory usage: %4.2f Mb.', $totalMemoryUsage / 1048576), $totalMemoryUsage / 1048576))
		;

		$totalMemoryUsage = rand(2, PHP_INT_MAX);

		$score
			->getMockController()
				->getTotalMemoryUsage = function() use ($totalMemoryUsage) { return $totalMemoryUsage; }
		;

		$memory->setWithRunner($runner);

		$this->assert
			->string($memory->toString())->isEqualTo(sprintf($locale->__('Total test memory usage: %4.2f Mb.', 'Total test memory usage: %4.2f Mb.', $totalMemoryUsage / 1048576), $totalMemoryUsage / 1048576))
		;

		$testsNumber = rand(2, PHP_INT_MAX);

		$runnerController->getTestsNumber = function () use ($testsNumber) { return $testsNumber; };

		$memory->setWithRunner($runner);

		$this->assert
			->string($memory->toString())->isEqualTo(sprintf($locale->__('Total tests memory usage: %4.2f Mb.', 'Total tests memory usage: %4.2f Mb.', $totalMemoryUsage / 1048576), $totalMemoryUsage / 1048576))
		;
	}
}

?>
