<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mock\mageekguy\atoum\reader as testedClass
;

require_once __DIR__ . '/../runner.php';

class reader extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isAbstract();
	}

	public function test__construct()
	{
		$this
			->if($reader = new testedClass())
			->then
				->object($reader->getAdapter())->isEqualTo(new atoum\adapter())
			->if($reader = new testedClass($adapter = new atoum\adapter()))
			->then
				->object($reader->getAdapter())->isIdenticalTo($adapter)
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
