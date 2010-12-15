<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\duration;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;
use \mageekguy\atoum\tests\units\report\fields;

require_once(__DIR__.'/../duration.php');
require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\runner\duration
{
	public function testClassConstants()
	{
		$this->assert
			->string(runner\duration\string::titlePrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$duration = new runner\duration\string();

		$this->assert
			->object($duration)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($duration->getValue())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$duration = new runner\duration\string($locale = new atoum\locale());

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

	public function test__toString()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getRunningDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); };

		$duration = new runner\duration\string($locale = new atoum\locale());

		$this->assert
			->castToString($duration)->isEqualTo(runner\duration\string::titlePrompt . $locale->_('Running duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner))->isEqualTo(runner\duration\string::titlePrompt . $locale->_('Running duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner, atoum\runner::runStart))->isEqualTo(runner\duration\string::titlePrompt . $locale->_('Running duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithRunner($runner, atoum\runner::runStop))->isEqualTo(runner\duration\string::titlePrompt . sprintf($locale->__('Running duration: %4.2f second.', 'Running duration: %4.2f seconds.', $runningDuration), $runningDuration) . PHP_EOL)
		;
	}
}

?>
