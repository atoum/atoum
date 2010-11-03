<?php

namespace mageekguy\atoum\tests\units\report\fields;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;

require_once(__DIR__ . '/../../../runner.php');

class runner extends atoum\test
{
	public function test__construct()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\report\fields\runner')
		;

		$field = new mock\mageekguy\atoum\report\fields\runner();

		$this->assert
			->object($field)->isInstanceOf('\mageekguy\atoum\report\field')
		;
	}
}

?>
