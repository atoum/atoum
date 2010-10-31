<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;

require_once(__DIR__ . '/../runner.php');

class runner extends atoum\test
{
	public function test__construct()
	{
		$runner = new atoum\runner();

		$this->assert
			->object($runner->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
			->variable($runner->getRunningDuration())->isNull()
		;

		$runner = new atoum\runner($adapter = new atoum\adapter());

		$this->assert
			->object($runner->getAdapter())->isIdenticalTo($adapter)
			->variable($runner->getRunningDuration())->isNull()
		;

	}
	public function testGetRunningDuration()
	{
		$adapter = new atoum\adapter();
		$adapter->microtime = function() { static $call = 0; return (++$call * 100); };
		$adapter->get_declared_classes = function() { return array(); };

		$runner = new atoum\runner($adapter);

		$this->assert
			->variable($runner->getRunningDuration())->isNull()
		;

		$runner->run();

		$this->assert
			->integer($runner->getRunningDuration())->isEqualTo(100)
		;
	}
}

?>
