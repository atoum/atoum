<?php

namespace atoum\tests\units;

use
	atoum,
	atoum\mock
;

require_once __DIR__ . '/../runner.php';

class writer extends atoum\test
{
	public function test__construct()
	{
		$writer = new \mock\atoum\writer();

		$this->assert
			->object($writer->getAdapter())->isInstanceOf('atoum\adapter')
		;

		$writer = new \mock\atoum\writer($adapter = new atoum\test\adapter());

		$this->assert
			->object($writer->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetAdapter()
	{
		$writer = new \mock\atoum\writer();

		$this->assert
			->object($writer->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($writer)
			->object($writer->getAdapter())->isIdenticalTo($adapter)
		;
	}
}
