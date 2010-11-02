<?php

namespace mageekguy\atoum\tests\units\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\test;

require_once(__DIR__ . '/../../../../runner.php');

class progress extends atoum\test
{
	public function test__construct()
	{
		$run = new test\progress();

		$this->assert
			->object($run)->isInstanceOf('\mageekguy\atoum\report\fields\test')
		;
	}
}

?>
