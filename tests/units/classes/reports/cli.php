<?php

namespace mageekguy\atoum\tests\units\reports;

use \mageekguy\atoum;
use \mageekguy\atoum\reports;

require_once(__DIR__ . '/../../runner.php');

class cli extends atoum\test
{
	public function test__construct()
	{
		$cli = new reports\cli();
	}
}

?>
