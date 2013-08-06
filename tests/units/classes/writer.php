<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mock\mageekguy\atoum\writer as testedClass
;

require_once __DIR__ . '/../runner.php';

class writer extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isAbstract();
	}

	public function test__construct()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->getAdapter())->isEqualTo(new atoum\adapter())
			->if($writer = new testedClass($adapter = new atoum\test\adapter()))
			->then
				->object($writer->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($writer)
				->object($writer->getAdapter())->isIdenticalTo($adapter)
				->object($writer->setAdapter())->isIdenticalTo($writer)
				->object($writer->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testReset()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->reset())->isIdenticalTo($writer)
		;
	}
}
