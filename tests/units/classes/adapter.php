<?php

namespace mageekguy\atoum\tests\units;

use
	atoum
;

require_once __DIR__ . '/../runner.php';

class adapter extends atoum
{
	public function test__call()
	{
		$this
			->given($adapter = new atoum\adapter())
			->then
				->string($adapter->md5($hash = uniqid()))->isEqualTo(md5($hash))
		;
	}
}
