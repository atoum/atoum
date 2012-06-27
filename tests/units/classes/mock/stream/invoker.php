<?php

namespace mageekguy\atoum\tests\units\mock\stream;

use
	mageekguy\atoum\test,
	mageekguy\atoum\mock\stream
;

require_once __DIR__ . '/../../../runner.php';

class invoker extends test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\test\adapter\invoker');
	}

	public function test__construct()
	{
		$this
			->if($invoker = new stream\invoker($methodName = uniqid()))
			->then
				->string($invoker->getMethodName())->isEqualTo($methodName)
		;
	}
}
