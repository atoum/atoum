<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

require_once __DIR__ . '/../runner.php';

class writer extends atoum\test
{
	public function test__construct()
	{
		$writer = new \mock\mageekguy\atoum\writer();

		$this->assert
			->object($writer->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
		;

		$writer = new \mock\mageekguy\atoum\writer($adapter = new atoum\test\adapter());

		$this->assert
			->object($writer->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetAdapter()
	{
		$writer = new \mock\mageekguy\atoum\writer();

		$this->assert
			->object($writer->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($writer)
			->object($writer->getAdapter())->isIdenticalTo($adapter)
		;
	}
}
