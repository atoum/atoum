<?php

namespace mageekguy\atoum\tests\units;

use
	atoum
;

require_once __DIR__ . '/../runner.php';

class adapter extends atoum
{
	public function test__construct()
	{
		$this
			->given($asserter = new \mock\mageekguy\atoum\asserter($generator = new atoum\asserter\generator()))
			->then
				->object($asserter->getGenerator())->isIdenticalTo($generator)
		;
	}

	public function test__call()
	{
		$this
			->given($adapter = new atoum\adapter())
			->then
				->string($adapter->md5($hash = uniqid()))->isEqualTo(md5($hash))
		;
	}
}
