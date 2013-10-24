<?php

namespace mageekguy\atoum\tests\units\php\mocker;

require_once __DIR__ . '/../../../runner.php';

use
	atoum,
	atoum\php\mocker,
	atoum\php\mocker\adapter as testedClass
;

class adapter extends atoum
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\test\adapter');
	}

	public function test__get()
	{
		$this
			->if($adapter = new testedClass())
			->then
				->object($invoker = $adapter->md5)->isEqualTo(new mocker\adapter\invoker('md5'))
				->object($adapter->md5)->isIdenticalTo($invoker)
		;
	}
}
