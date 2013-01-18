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
				->boolean($controller->stream_eof())->isFalse()
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
				->object($controller->linkStatsTo($otherController))->isIdenticalTo($controller)
				->string($controller->getMode())->isEqualTo($otherController->getMode())
			->if($controller->isNotReadable())
			->then
				->string($controller->getMode())
					->isEqualTo('000')
					->isEqualTo($otherController->getMode())
		;
	}

	public function testContains()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->contains('abcdefghijklmnopqrstuvwxyz'))->isIdenticalTo($controller)
				->string($controller->stream_read(1))->isEqualTo('a')
				->boolean($controller->stream_eof())->isFalse()
				->string($controller->stream_read(1))->isEqualTo('b')
				->boolean($controller->stream_eof())->isFalse()
				->string($controller->stream_read(2))->isEqualTo('cd')
				->boolean($controller->stream_eof())->isFalse()
				->string($controller->stream_read(4096))->isEqualTo('efghijklmnopqrstuvwxyz')
				->boolean($controller->stream_eof())->isTrue()
				->string($controller->stream_read(1))->isEmpty()
		;
	}

	public function testIsEmpty()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->object($controller->isEmpty())->isIdenticalTo($controller)
				->string($controller->getContents())->isEmpty()
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
				->boolean($controller->fopen('r'))->isFalse()
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
				->variable($controller->fopen('r'))->isNotFalse()
		;
	}

	public function testIsNotReadable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isNotReadable())->isIdenticalTo($controller)
				->string($controller->getMode())->isEqualTo('000')
		;
	}

	public function testIsReadable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isReadable())->isIdenticalTo($controller)
				->string($controller->getMode())->isEqualTo('444')
		;
	}

	public function testIsNotWritable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isNotWritable())->isIdenticalTo($controller)
				->string($controller->getMode())->isEqualTo('444')
		;
	}

	public function testIsWritable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->isNotWritable())
			->then
				->object($controller->isWritable())->isIdenticalTo($controller)
				->string($controller->getMode())->isEqualTo('644')
		;
	}

	public function testSeek()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->seek(0))->isFalse()
				->boolean($controller->seek(1))->isTrue()
			->if($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->boolean($controller->seek(0))->isFalse()
				->boolean($controller->seek(1))->isTrue()
				->string($controller->read(1))->isEqualTo('b')
				->boolean($controller->seek(25))->isTrue()
				->string($controller->read(1))->isEqualTo('z')
				->boolean($controller->seek(26))->isFalse()
				->string($controller->read(1))->isEmpty()
				->boolean($controller->seek(0))->isTrue()
				->string($controller->read(1))->isEqualTo('a')
				->boolean($controller->seek(-1, SEEK_END))->isTrue()
				->string($controller->read(1))->isEqualTo('z')
				->boolean($controller->seek(-26, SEEK_END))->isTrue()
				->string($controller->read(1))->isEqualTo('a')
				->boolean($controller->seek(-27, SEEK_END))->isTrue()
				->string($controller->read(1))->isEmpty()
		;
	}

	public function testEof()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->eof())->isFalse()
			->if($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->boolean($controller->eof())->isFalse()
			->if($controller->seek(26))
			->then
				->boolean($controller->eof())->isFalse()
			->if($controller->seek(27))
			->then
				->boolean($controller->eof())->isFalse()
			->if($controller->read(1))
			->then
				->boolean($controller->eof())->isTrue()
		;
	}

	public function testTell()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->integer($controller->tell())->isZero()
			->if($controller->seek($offset = rand(1, 4096)))
			->then
				->integer($controller->tell())->isEqualTo($offset)
		;
	}
}
