<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium;

use \mageekguy\atoum;

require_once(__DIR__ . '/../../../runner.php');

class responseStatus extends atoum\test
{
	public function testAllConstantsAreIntegers()
	{
		$reflector = new \ReflectionClass('\mageekguy\atoum\tests\functional\selenium\responseStatus');
		
		foreach ($reflector->getConstants() as $constant)
		{
			$this->assert->integer($constant);
		}
	}
}

?>
