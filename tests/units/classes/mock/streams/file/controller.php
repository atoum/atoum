<?php

namespace mageekguy\atoum\tests\units\mock\streams\file;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\streams\file\controller as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class controller extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\mock\stream\controller');
	}

	public function testCanNotBeOpened()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->canNotBeOpened())->isIdenticalTo($controller)
				->object($controller->fopen)->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
				->object($controller->FOPEN)->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
				->boolean($controller->invoke('fopen', array('r')))->isFalse()
		;
	}
}
