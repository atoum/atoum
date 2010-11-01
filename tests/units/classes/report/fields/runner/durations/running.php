<?php

namespace mageekguy\atoum\tests\units\report\fields\durations\runnings;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner\durations;

require_once(__DIR__ . '/../../../../../runner.php');

class running extends atoum\test
{
	public function test__construct()
	{
		$running = new durations\running();

		$this->assert
			->object($running)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($running->getDuration())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$running = new durations\running($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runningDuration = rand(0, PHP_INT_MAX);

		$runner = new mock\mageekguy\atoum\runner();
		$runner
			->getMockController()
				->getRunningDuration = function () use ($runningDuration) { return $runningDuration; }
		;

		$this->assert
			->variable($running->getDuration())->isNull()
			->object($running->setWithRunner($runner))->isIdenticalTo($running)
			->integer($running->getDuration())->isIdenticalTo($runningDuration)
		;
	}

	public function testToString()
	{
		$running = new durations\running($locale = new atoum\locale());

		$this->assert
			->string($running->toString())->isEqualTo($locale->_('Running duration: unknown.'))
		;

		$runningDuration = rand(0, PHP_INT_MAX);

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();
		$runner
			->getMockController()
				->getRunningDuration = function () use ($runningDuration) { return $runningDuration; }
		;

		$running->setWithRunner($runner);

		$this->assert
			->string($running->toString())->isEqualTo(sprintf($locale->__('Running duration: %4.2f second.', 'Running duration: %4.2f seconds.', $runningDuration), $runningDuration))
		;
	}
}

?>
