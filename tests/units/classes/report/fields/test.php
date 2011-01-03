<?php

namespace mageekguy\atoum\tests\units\report\fields;

use \mageekguy\atoum;

require_once(__DIR__ . '/../../../runner.php');

abstract class test extends atoum\test
{
	abstract public function testSetWithTest();
}

?>
