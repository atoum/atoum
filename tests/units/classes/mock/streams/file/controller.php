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

	public function test__construct()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->string($controller->getContents())->isEmpty()
				->integer($controller->getPointer())->isZero()
				->string($controller->getMode())->isEqualTo('644')
		;
	}

	public function testLinkContentsTo()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($otherController = new testedClass(uniqid()))
			->then
				->object($controller->linkContentsTo($otherController))->isIdenticalTo($controller)
				->string($controller->getContents())->isEqualTo($otherController->getContents())
			->if($controller->contains($data = uniqid()))
			->then
				->string($controller->getContents())
					->isEqualTo($data)
					->isEqualTo($otherController->getContents())
			->if($controller->contains($otherData = uniqid()))
			->then
				->string($controller->getContents())
					->isEqualTo($otherData)
					->isEqualTo($otherController->getContents())
		;
	}

	public function testLinkModeTo()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($otherController = new testedClass(uniqid()))
			->then
				->object($controller->linkModeTo($otherController))->isIdenticalTo($controller)
				->string($controller->getMode())->isEqualTo($otherController->getMode())
			->if($controller->canNotBeRead())
			->then
				->string($controller->getMode())
					->isEqualTo('000')
					->isEqualTo($otherController->getMode())
		;
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
				->array($controller->invoke('url_stat'))->isEqualTo(array('uid' => getmyuid(), 'mode' => 0100000))
		;
	}

	public function testCanRead()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->canBeRead())->isIdenticalTo($controller)
				->array($controller->invoke('url_stat'))->isEqualTo(array('uid' => getmyuid(), 'mode' => 0100444))
		;
	}

	public function testCanNotBeWrited()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->canNotBeWrited())->isIdenticalTo($controller)
				->array($controller->invoke('url_stat'))->isEqualTo(array('uid' => getmyuid(), 'mode' => 0100444))
		;
	}

	public function testCanBeWrited()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->canNotBeWrited())
			->then
				->object($controller->canBeWrited())->isIdenticalTo($controller)
				->array($controller->invoke('url_stat'))->isEqualTo(array('uid' => getmyuid(), 'mode' => 0100644))
		;
	}

	public function testContains()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->contains('abcdefghijklmnopqrstuvwxyz'))->isIdenticalTo($controller)
				->string($controller->invoke('stream_read', array(1)))->isEqualTo('a')
				->string($controller->invoke('stream_read', array(1)))->isEqualTo('b')
				->string($controller->invoke('stream_read', array(2)))->isEqualTo('cd')
				->string($controller->invoke('stream_read', array(8192)))->isEqualTo('efghijklmnopqrstuvwxyz')
				->string($controller->invoke('stream_read', array(1)))->isEmpty()
		;
	}
}
