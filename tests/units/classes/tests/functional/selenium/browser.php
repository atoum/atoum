<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium;

use \mageekguy\atoum;

require_once(__DIR__ . '/../../../runner.php');

class browser extends atoum\test
{
	public function testAllConstantsAreString()
	{
		$reflector = new \ReflectionClass('\mageekguy\atoum\tests\functional\selenium\browser');
		
		foreach ($reflector->getConstants() as $constant)
		{
			$this->assert->string($constant);
		}
	}
}

?>
