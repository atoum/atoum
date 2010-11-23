<?php

namespace mageekguy\atoum\tests\units\exceptions\logic;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions\logic;

require_once(__DIR__ . '/../../../runner.php');

class invalidArgument extends atoum\test
{
	public function test__construct()
	{
		$invalidArgumentException = new logic\invalidArgument();

		$this->assert
			->object($invalidArgumentException)
				->isInstanceOf('\logicException')
				->isInstanceOf('\invalidArgumentException')
				->isInstanceOf('\mageekguy\atoum\exception')
		;
	}
}

?>
