<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../runner.php');

class duration extends atoum\test
{
	public function test__construct()
	{
		$duration = new runner\duration();

		$this->assert
			->object($duration)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($duration->getValue())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$duration = new runner\duration($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getRunningDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); };

		$this->assert
			->variable($duration->getValue())->isNull()
			->object($duration->setWithRunner($runner))->isIdenticalTo($duration)
			->variable($duration->getValue())->isNull()
			->object($duration->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($duration)
			->variable($duration->getValue())->isNull()
			->object($duration->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($duration)
			->integer($duration->getValue())->isEqualTo($runningDuration)
		;
	}

	public function testToString()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getRunningDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); };

		$duration = new runner\duration($locale = new atoum\locale());

		$this->assert
			->string($duration->toString())->isEqualTo($locale->_('Running duration: unknown.'))
			->string($duration->setWithRunner($runner)->toString())->isEqualTo('Running duration: unknown.')
			->string($duration->setWithRunner($runner, atoum\runner::runStart)->toString())->isEqualTo('Running duration: unknown.')
			->string($duration->setWithRunner($runner, atoum\runner::runStop)->toString())->isEqualTo(sprintf($locale->__('Running duration: %4.2f second.', 'Running duration: %4.2f seconds.', $runningDuration), $runningDuration))
		;
	}
}

?>
