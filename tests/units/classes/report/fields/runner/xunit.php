<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../runner.php');

class xunit extends \mageekguy\atoum\tests\units\report\fields\runner
{
	public function test__construct()
	{
		$xunit = new runner\xunit();

		$this->assert
			->object($xunit)->isInstanceOf('\mageekguy\atoum\report\fields\runner');
		;
	}

	public function testSetWithRunner()
	{
		$xunit = new runner\xunit();

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = function () use ($score) { return $score; };

		$this->assert
			->object($xunit->setWithRunner($runner))->isIdenticalTo($xunit)
			->mock($runner)->notCall('getScore')
			->object($xunit->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($xunit)
			->mock($runner)->notCall('getScore')
			->object($xunit->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($xunit)
			->mock($runner)->call('getScore')
		;
	}
}

?>
