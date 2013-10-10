<?php

namespace mageekguy\atoum\tests\units\test\adapter\call\decorators;

require __DIR__ . '/../../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter\call,
	mageekguy\atoum\test\adapter\call\decorators\addClass as testedClass

;

class addClass extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\test\adapter\call\decorator');
	}

	public function test__construct()
	{
		$this
			->if($decorator = new testedClass($class = uniqid()))
			->then
				->string($decorator->getClass())->isEqualTo($class)
				->object($decorator->getArgumentsDecorator())->isEqualTo(new call\arguments\decorator())
			->if($decorator = new testedClass($object = new \mock\object()))
			->then
				->string($decorator->getClass())->isEqualTo(get_class($object))
				->object($decorator->getArgumentsDecorator())->isEqualTo(new call\arguments\decorator())
		;
	}

	public function testDecorate()
	{
		$this
			->if($decorator = new testedClass($class = uniqid()))
			->then
				->string($decorator->decorate(new call()))->isEmpty()
				->string($decorator->decorate(new call($function = uniqid())))->isEqualTo($class . '::' . $function . '(*)')
				->string($decorator->decorate(new call(null, array())))->isEmpty()
				->string($decorator->decorate(new call($function = uniqid(), array())))->isEqualTo($class . '::' . $function . '()')
				->string($decorator->decorate(new call($function = uniqid(), $arguments = array(uniqid(), uniqid()))))->isEqualTo($class . '::' . $function . '(' . $decorator->getArgumentsDecorator()->decorate($arguments) . ')')
		;
	}
}
