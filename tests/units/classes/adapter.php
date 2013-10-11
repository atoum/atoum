<?php

namespace mageekguy\atoum\tests\units;

use
	atoum,
	mageekguy\atoum\adapter as testedClass
;

require_once __DIR__ . '/../runner.php';

class adapter extends atoum
{
	public function test__call()
	{
		$this
			->given($adapter = new testedClass())
			->then
				->string($adapter->md5($hash = uniqid()))->isEqualTo(md5($hash))
		;
	}

	public function testInvoke()
	{
		$this
			->given($adapter = new testedClass())
			->then
				->string($adapter->invoke('md5', array($hash = uniqid())))->isEqualTo(md5($hash))
		;
	}
}
