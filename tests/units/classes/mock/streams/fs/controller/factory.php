<?php

namespace atoum\tests\units\mock\streams\fs\controller;

use
	atoum,
	atoum\mock\streams\fs\controller,
	atoum\mock\streams\fs\controller\factory as testedClass
;

require __DIR__ . '/../../../../../runner.php';

class factory extends atoum\test
{
	public function testBuild()
	{
		$this
			->given($factory = new testedClass())
			->then
				->object($factory->build($name = uniqid()))->isEqualTo(new controller($name))
		;
	}
}
