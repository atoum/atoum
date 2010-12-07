<?php

namespace mageekguy\atoum\tests\units\score\coverage\tokenizer;

use mageekguy\atoum;
use mageekguy\atoum\score\coverage\tokenizer;

require_once(__DIR__ . '/../../../../runner.php');

class exception extends atoum\test
{
	public function test__construct()
	{
		$exception = new tokenizer\exception(uniqid());

		$this->assert
			->object($exception)
				->isInstanceOf('\mageekguy\atoum\exception')
				->isInstanceOf('\runtimeException')
		;
	}
}

?>
