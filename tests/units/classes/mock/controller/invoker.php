<?php

namespace mageekguy\atoum\tests\units\mock\controller;

require_once __DIR__ . '/../../../runner.php';

use
	atoum,
	atoum\mock\controller\invoker as testedClass
;

class invoker extends atoum
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\test\adapter\invoker');
	}

	public function test__construct()
	{
		$this
			->if($invoker = new testedClass($method = uniqid()))
			->then
				->string($invoker->getFunction())->isEqualTo($method)
				->variable($invoker->getMock())->isNull()
			->if($invoker = new testedClass($method = uniqid(), $mock = new \mock\foo()))
			->then
				->string($invoker->getFunction())->isEqualTo($method)
				->object($invoker->getMock())->isIdenticalTo($mock)
		;
	}

	public function testReturnThis()
	{
		$this
			->if($invoker = new testedClass($method = uniqid(), $mock = new \mock\foo()))
			->then
				->object($invoker->returnThis())->isIdenticalTo($invoker)
				->object($invoker->invoke())->isIdenticalTo($mock)
		;
	}
}
