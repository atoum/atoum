<?php

namespace mageekguy\atoum\tests\units\mock\streams\fs\directory;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\streams\fs\directory\controller as testedClass
;

require_once __DIR__ . '/../../../../../runner.php';

class controller extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\mock\stream\controller');
	}

	public function test__construct()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->integer($controller->getMode())->isEqualTo(755)
		;
	}
}
