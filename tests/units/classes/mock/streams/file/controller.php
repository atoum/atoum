<?php

namespace mageekguy\atoum\tests\units\mock\streams\file;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\streams\file\controller as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class controller extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\mock\stream\controller');
	}

	public function testCanNotBeOpened()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->canNotBeOpened())->isIdenticalTo($controller)
				->object($controller->fopen)->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
				->object($controller->FOPEN)->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
				->boolean($controller->invoke('fopen', array('r')))->isFalse()
		;
	}

	public function testCanBeOpened()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->canNotBeOpened())
			->then
				->object($controller->canBeOpened())->isIdenticalTo($controller)
				->object($controller->fopen)->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
				->object($controller->FOPEN)->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
				->variable($controller->invoke('fopen', array('r')))->isNotFalse()
		;
	}

	public function testCanNotBeRead()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->canNotBeRead())->isIdenticalTo($controller)
				->array($controller->invoke('stat'))->isEqualTo(array('mode' => 32768))
		;
	}

	public function testCanRead()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->canBeRead())->isIdenticalTo($controller)
				->array($controller->invoke('stat'))->isEqualTo(array('mode' => 33188))
		;
	}

	public function testCanNotBeWrited()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->canNotBeWrited())->isIdenticalTo($controller)
				->array($controller->invoke('stat'))->isEqualTo(array('uid' => getmyuid(), 'mode' => 33060))
		;
	}

	public function testCanBeWrited()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->canNotBeWrited())
			->then
				->object($controller->canBeWrited())->isIdenticalTo($controller)
				->array($controller->invoke('stat'))->isEqualTo(array('uid' => getmyuid(), 'mode' => 33188))
		;
	}

}
