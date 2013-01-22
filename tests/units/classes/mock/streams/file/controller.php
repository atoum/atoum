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
				->integer($controller->getMode())->isEqualTo(644)
				->boolean($controller->stream_eof())->isFalse()
				->array($controller->stat())->isNotEmpty()
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
				->integer($controller->getMode())->isEqualTo($otherController->getMode())
			->if($controller->isNotReadable())
			->then
				->integer($controller->getMode())
					->isEqualTo(200)
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

	public function testExists()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->exists())->isIdenticalTo($controller)
				->array($controller->stat())->isNotEmpty()
			->if($controller->notExists())
			->then
				->object($controller->exists())->isIdenticalTo($controller)
				->array($controller->stat())->isNotEmpty()
		;
	}

	public function testNotExists()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->notExists())->isIdenticalTo($controller)
				->boolean($controller->stat())->isFalse()
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
				->integer($controller->getMode())->isEqualTo(200)
				->object($controller->isNotReadable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(200)
		;
	}

	public function testIsReadable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
			->if($controller->isNotReadable())
			->then
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
		;
	}

	public function testIsNotWritable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isNotWritable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(444)
				->object($controller->isNotWritable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(444)
		;
	}

	public function testIsWritable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->isNotWritable())
			->then
				->object($controller->isWritable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(666)
				->object($controller->isWritable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(666)
		;
	}

	public function testIsExecutable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isExecutable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(755)
				->object($controller->isExecutable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(755)
		;
	}

	public function testIsNotExecutable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->isExecutable())
			->then
				->object($controller->isNotExecutable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
				->object($controller->isNotExecutable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
		;
	}

	public function testOpen()
	{
		$this
			->assert('Use r and r+ mode')
				->if($controller = new testedClass(uniqid()))
				->then
					->boolean($controller->open('r', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isZero()
					->boolean($controller->open('r+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('r', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getStream())
					->boolean($controller->open('r+', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getStream())
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->open('r', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isZero()
					->boolean($controller->open('r+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('r', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getStream())
					->boolean($controller->open('r+', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getStream())
				->if($controller->notExists())
				->then
					->boolean($controller->open('r', 0))->isFalse()
					->boolean($controller->open('r+', 0))->isFalse()
					->boolean($controller->open('r', STREAM_USE_PATH, $path))->isFalse()
					->variable($path)->isNull()
					->boolean($controller->open('r+', STREAM_USE_PATH, $path))->isFalse()
					->variable($path)->isNull()
				->if($controller->exists())
				->and($controller->isNotReadable())
				->then
					->boolean($controller->open('r', 0))->isFalse()
					->boolean($controller->open('r+', 0))->isFalse()
					->boolean($controller->open('r', STREAM_USE_PATH, $path))->isFalse()
					->variable($path)->isNull()
					->boolean($controller->open('r+', STREAM_USE_PATH, $path))->isFalse()
					->variable($path)->isNull()
				->if($controller->isReadable())
				->and($controller->isNotWritable())
					->boolean($controller->open('r', 0))->isTrue()
					->boolean($controller->open('r+', 0))->isFalse()
					->boolean($controller->open('r', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getStream())
					->boolean($controller->open('r+', STREAM_USE_PATH, $path))->isFalse()
					->variable($path)->isNull()
			->assert('Use w and w+ mode')
				->if($controller = new testedClass(uniqid()))
				->then
					->boolean($controller->open('w', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('w+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('w', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getStream())
					->boolean($controller->open('w+', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getStream())
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->open('w', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('w+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->isNotWritable())
				->then
					->boolean($controller->open('w', 0))->isFalse()
					->boolean($controller->open('w+', 0))->isFalse()
			->assert('Use c and c+ mode')
				->if($controller = new testedClass(uniqid()))
				->then
					->boolean($controller->open('c', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('c+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('c', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getStream())
					->boolean($controller->open('c+', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getStream())
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->open('c', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('c+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->isNotWritable())
				->then
					->boolean($controller->open('c', 0))->isFalse()
					->boolean($controller->open('c+', 0))->isFalse()
			->assert('Use a and a+ mode')
				->if($controller = new testedClass(uniqid()))
				->then
					->boolean($controller->open('a', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('a+', 0))->isTrue()
					->integer($controller->tell())->isEqualTo(1)
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->open('a', 0))->isTrue()
					->integer($controller->tell())->isEqualTo(26)
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->isNotWritable())
				->then
					->boolean($controller->open('a', 0))->isFalse()
					->boolean($controller->open('a+', 0))->isFalse()
				->if($controller = new testedClass(uniqid()))
				->if($controller->isWritable())
				->and($controller->isNotReadable())
				->then
					->boolean($controller->open('a', 0))->isTrue()
					->boolean($controller->open('a+', 0))->isFalse()
			->assert('Use x and x+ mode')
				->if($controller = new testedClass(uniqid()))
				->then
					->boolean($controller->open('x', 0))->isFalse()
					->boolean($controller->open('x+', 0))->isFalse()
				->if($controller->notExists())
				->then
					->boolean($controller->open('x', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(0)
					->boolean($controller->open('x+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->open('x', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(0)
					->boolean($controller->open('x+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->isNotReadable())
				->then
					->boolean($controller->open('x', 0))->isFalse()
					->boolean($controller->open('x+', 0))->isFalse()
				->if($controller->isReadable())
				->and($controller->isNotWritable())
				->then
					->boolean($controller->open('x', 0))->isTrue()
					->boolean($controller->open('x+', 0))->isFalse()
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
			->if($controller = new testedClass(uniqid()))
			->and($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->and($controller->read(4096))
			->then
				->boolean($controller->eof())->isTrue()
				->boolean($controller->seek(0))->isTrue()
				->boolean($controller->eof())->isFalse()
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

	public function testRead()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->string($controller->read(1))->isEmpty()
				->boolean($controller->eof())->isTrue()
			->if($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->string($controller->read(1))->isEqualTo('a')
				->boolean($controller->eof())->isFalse()
			->if($controller->seek(6))
			->then
				->string($controller->read(1))->isEqualTo('g')
				->string($controller->read(4096))->isEqualTo('hijklmnopqrstuvwxyz')
				->boolean($controller->eof())->isTrue()
				->string($controller->read(1))->isEmpty()
		;
	}

	public function testWrite()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->integer($controller->write('a'))->isZero()
				->integer($controller->tell())->isZero()
			->if($controller->open('r', 0))
			->then
				->integer($controller->write('a'))->isZero()
				->integer($controller->tell())->isZero()
			->if($controller->open('w', 0))
			->then
				->integer($controller->write('a'))->isEqualTo(1)
				->integer($controller->tell())->isEqualTo(1)
				->integer($controller->write('bcdefghijklmnopqrstuvwxyz'))->isEqualTo(25)
				->integer($controller->tell())->isEqualTo(26)
		;
	}

	public function testMetadata()
	{
		if (version_compare(phpversion(), '5.4.0', '<') === true)
		{
			$this->skip('It\'s not possible to manage stream\'s metadata before PHP 5.4.0');
		}

		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->metadata(STREAM_META_ACCESS, 755))->isTrue()
				->integer($controller->getMode())->isEqualTo(755)
		;
	}

	public function testSetStream()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->setStream($newName = uniqid()))->isTrue()
				->string($controller->getStream())->isEqualTo($newName)
		;
	}
}
