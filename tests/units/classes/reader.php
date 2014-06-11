<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
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
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->getAdapter())->isEqualTo(new atoum\adapter())
			->if($this->newTestedInstance($adapter = new atoum\adapter()))
			->then
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setAdapter($adapter = new atoum\adapter()))->isTestedInstance
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
		;
	}
}
