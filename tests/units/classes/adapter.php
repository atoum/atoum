<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once(__DIR__ . '/../runner.php');

class adapter extends atoum\test
{
	public function test__call()
	{
		$adapter = new atoum\adapter();

		$this->assert->string($adapter->md5($hash = uniqid()))->isEqualTo(md5($hash));
	}
}

?>
