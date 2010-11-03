<?php

namespace mageekguy\atoum\tests\units\report\fields;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../runner.php');

class test extends atoum\test
{
	public function test__construct()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\report\fields\test')
		;

		$field = new mock\mageekguy\atoum\report\fields\test();

		$this->assert
			->object($field)->isInstanceOf('\mageekguy\atoum\report\field')
		;
	}
}

?>
