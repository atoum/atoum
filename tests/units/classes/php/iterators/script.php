<?php

namespace mageekguy\atoum\tests\units\php\iterators;

use
	\mageekguy\atoum,
	\mageekguy\atoum\php\iterators
;

require_once(__DIR__ . '/../../../runner.php');

class script extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubclassOf('\mageekguy\atoum\php\iterator')
		;
	}
}

?>
