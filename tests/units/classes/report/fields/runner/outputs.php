<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;

require_once(__DIR__ . '/../../../../runner.php');

abstract class outputs extends atoum\test
{
	abstract public function testSetWithRunner();
}

?>
