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
		$this->string($this->newTestedInstance->md5($hash = uniqid()))->isEqualTo(md5($hash));
	}

	public function testInvoke()
	{
		$this->string($this->newTestedInstance->invoke('md5', array($hash = uniqid())))->isEqualTo(md5($hash));
	}
}
