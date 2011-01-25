<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report\fields\runner;
require_once(__DIR__ . '/../runner.php');
require_once(__DIR__ . '/../../../../runner.php');

class xunit extends \mageekguy\atoum\tests\units\report\fields\runner
{
	public function test__construct()
	{
		$xun = new runner\xunit();

		$this->assert
			->object($xun)->isInstanceOf('\mageekguy\atoum\report\fields\runner');
		;
	}

	public function testSetWithRunner()
	{
		$xun = new runner\xunit();

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		
		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = function () use ($score) { return $score; };

		$this->assert
			->object($xun->setWithRunner($runner))->isIdenticalTo($xun)
			->mock($runner)
				->integer(sizeof($runner->getMockController()->getCalls()))->isEqualTo(0)
			->object($xun->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($xun)
			->mock($runner)
				->integer(sizeof($runner->getMockController()->getCalls()))->isEqualTo(0)
			->object($xun->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($xun)
			->mock($runner)
				->call('getScore')
		;
	}
}

?>
