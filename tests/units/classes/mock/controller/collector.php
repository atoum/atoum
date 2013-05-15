<?php

namespace mageekguy\atoum\tests\units\mock\controller;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\controller,
	mageekguy\atoum\mock\controller\collector as testedClass
;

require __DIR__ . '../../../runner.php';

class collector extends atoum\test
{
	public function testAdd()
	{
		$this
			->if(testedClass::add($mock = new \mock\foo(), $controller = new controller()))
			->then
				->object(testedClass::get($mock))->isIdenticalTo($controller)
		;
	}

	public function testGet()
	{
		$this
			->if($mock = new \mock\foo())
			->then
				->object(testedClass::get($mock))->isIdenticalTo($mock->getMockController())
			->if($otherMock = new \mock\foo())
			->then
				->object(testedClass::get($mock))->isIdenticalTo($mock->getMockController())
				->object(testedClass::get($otherMock))->isIdenticalTo($otherMock->getMockController())
		;
	}

	public function testRemove()
	{
		$this
			->if(testedClass::add($mock = new \mock\foo(), new controller()))
			->and(testedClass::remove($mock))
			->then
				->variable(testedClass::get($mock))->isNull()
			->if(testedClass::add($mock, $controller = new controller()))
			->and(testedClass::add($otherMock = new \mock\foo(), $otherController = new controller()))
			->and(testedClass::remove($mock))
			->then
				->variable(testedClass::get($mock))->isNull()
				->object(testedClass::get($otherMock))->isIdenticalTo($otherController)
		;
	}

	public function testClean()
	{
		$this
			->if($mock = new \mock\foo())
			->and(testedClass::clean())
			->then
				->variable(testedClass::get($mock))->isNull()
			->if(testedClass::add($mock = new \mock\foo(), new controller()))
			->and(testedClass::add($otherMock = new \mock\foo(), new controller()))
			->and(testedClass::clean())
			->then
				->variable(testedClass::get($mock))->isNull()
				->variable(testedClass::get($otherMock))->isNull()
		;
	}
}
