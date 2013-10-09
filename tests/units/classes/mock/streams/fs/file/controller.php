<?php

namespace mageekguy\atoum\tests\units\mock\streams\fs\file;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\mock\streams\fs\file\controller as testedClass
;

require_once __DIR__ . '/../../../../../runner.php';

class controller extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\mock\streams\fs\controller');
	}

	public function test__construct()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->string($controller->getContents())->isEmpty()
				->integer($controller->getPointer())->isZero()
				->integer($controller->getPermissions())->isEqualTo(644)
				->boolean($controller->stream_eof())->isFalse()
				->array($controller->stream_stat())->isNotEmpty()
		;
	}

	public function test__set()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->stream_open = false)
			->then
				->boolean($controller->stream_open(uniqid(), 'r', uniqid()))->isFalse()
				->exception(function() use ($controller) { $controller->mkdir = true; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Unable to override streamWrapper::mkdir() for file')
		;
	}

	public function testDuplicate()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($duplicatedController = $controller->duplicate())->isEqualTo($controller)
			->if($controller->setPath($path = uniqid()))
			->then
				->string($duplicatedController->getPath())->isEqualTo($path)
			->if($controller->stream_lock(LOCK_SH))
			->then
				->object($duplicatedController->getCalls())->isEqualTo($controller->getCalls())
			->if($controller->stream_lock = function() {})
			->then
				->array($duplicatedController->getInvokers())->isEqualTo($controller->getInvokers())
			->if($controller->setContents(uniqid()))
			->then
				->string($duplicatedController->getContents())->isEqualTo($controller->getContents())
			->if($controller->isNotReadable())
			->and($controller->isNotWritable())
			->and($controller->isNotExecutable())
			->then
				->integer($duplicatedController->getPermissions())->isEqualTo($controller->getPermissions())
			->if($controller->notExists())
			->then
				->boolean($duplicatedController->stream_stat())->isEqualTo($controller->stream_stat())
		;
	}

	public function testContains()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->contains('abcdefghijklmnopqrstuvwxyz'))->isIdenticalTo($controller)
			->if($controller->stream_open(uniqid(), 'r', 0))
			->then
				->string($controller->stream_read(1))->isEqualTo('a')
				->boolean($controller->stream_eof())->isFalse()
				->string($controller->stream_read(1))->isEqualTo('b')
				->boolean($controller->stream_eof())->isFalse()
				->string($controller->stream_read(2))->isEqualTo('cd')
				->boolean($controller->stream_eof())->isFalse()
				->string($controller->stream_read(4096))->isEqualTo('efghijklmnopqrstuvwxyz')
				->boolean($controller->stream_eof())->isFalse()
				->string($controller->stream_read(1))->isEmpty()
				->boolean($controller->stream_eof())->isTrue()
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
				->array($controller->stream_stat())->isNotEmpty()
			->if($controller->notExists())
			->then
				->object($controller->exists())->isIdenticalTo($controller)
				->array($controller->stream_stat())->isNotEmpty()
		;
	}

	public function testNotExists()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->notExists())->isIdenticalTo($controller)
				->boolean($controller->stream_stat())->isFalse()
		;
	}

	public function testIsNotReadable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isNotReadable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(200)
				->object($controller->isNotReadable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(200)
		;
	}

	public function testIsReadable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(644)
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(644)
			->if($controller->isNotReadable())
			->then
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(644)
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(644)
		;
	}

	public function testIsNotWritable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isNotWritable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(444)
				->object($controller->isNotWritable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(444)
		;
	}

	public function testIsWritable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->isNotWritable())
			->then
				->object($controller->isWritable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(666)
				->object($controller->isWritable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(666)
		;
	}

	public function testIsExecutable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isExecutable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(755)
				->object($controller->isExecutable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(755)
		;
	}

	public function testIsNotExecutable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->isExecutable())
			->then
				->object($controller->isNotExecutable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(644)
				->object($controller->isNotExecutable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(644)
		;
	}

	public function testStreamOpen()
	{
		$this
			->assert('Use r and r+ mode')
				->if($controller = new testedClass(uniqid()))
				->and($controller->setCalls($calls = new \mock\mageekguy\atoum\test\adapter\calls()))
				->and($usePath = null)
				->then
					->boolean($controller->stream_open($path = uniqid(), 'z', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'z', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'z', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'z', STREAM_REPORT_ERRORS)))->once()
					->error('Operation timed out', E_USER_WARNING)->exists()
					->boolean($controller->stream_open($path = uniqid(), 'r', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('a'))->isZero()
					->boolean($controller->stream_open($path = uniqid(), 'r+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('a'))->isEqualTo(1)
					->boolean($controller->stream_open($path = uniqid(), 'r', STREAM_USE_PATH, $usePath))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', STREAM_USE_PATH, null)))->once()
					->string($usePath)->isEqualTo($controller->getPath())
					->boolean($controller->stream_open($path = uniqid(), 'r+', STREAM_USE_PATH, $usePath))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', STREAM_USE_PATH, $usePath)))->once()
					->string($usePath)->isEqualTo($controller->getPath())
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->and($usePath = null)
				->then
					->boolean($controller->stream_open($path = uniqid(), 'r', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEqualTo('a')
					->integer($controller->stream_write('a'))->isZero()
					->boolean($controller->stream_open($path = uniqid(), 'r+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEqualTo('a')
					->integer($controller->stream_write('a'))->isEqualTo(1)
					->boolean($controller->stream_open($path = uniqid(), 'r', STREAM_USE_PATH, $usePath))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', STREAM_USE_PATH, null)))->once()
					->string($usePath)->isEqualTo($controller->getPath())
					->boolean($controller->stream_open($path = uniqid(), 'r+', STREAM_USE_PATH, $usePath))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', STREAM_USE_PATH, $usePath)))->once()
					->string($usePath)->isEqualTo($controller->getPath())
				->if($controller->notExists())
				->and($usePath = null)
				->then
					->boolean($controller->stream_open($path = uniqid(), 'r', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'r+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'r', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', STREAM_REPORT_ERRORS)))->once()
					->error('No such file or directory', E_USER_WARNING)->exists()
					->boolean($controller->stream_open($path = uniqid(), 'r+', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', STREAM_REPORT_ERRORS)))->once()
					->error('No such file or directory', E_USER_WARNING)->exists()
					->boolean($controller->stream_open($path = uniqid(), 'r+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'r', STREAM_USE_PATH, $usePath))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', STREAM_USE_PATH, null)))->once()
					->variable($usePath)->isNull()
					->boolean($controller->stream_open($path = uniqid(), 'r+', STREAM_USE_PATH, $usePath))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', STREAM_USE_PATH, null)))->once()
					->variable($usePath)->isNull()
				->if($controller->exists())
				->and($controller->isNotReadable())
				->and($usePath = null)
				->then
					->boolean($controller->stream_open($path = uniqid(), 'r', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'r+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'r', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', STREAM_REPORT_ERRORS)))->once()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->stream_open($path = uniqid(), 'r+', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', STREAM_REPORT_ERRORS)))->once()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->stream_open($path = uniqid(), 'r', STREAM_USE_PATH, $usePath))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', STREAM_USE_PATH, null)))->once()
					->variable($usePath)->isNull()
					->boolean($controller->stream_open($path = uniqid(), 'r+', STREAM_USE_PATH, $usePath))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', STREAM_USE_PATH, null)))->once()
					->variable($usePath)->isNull()
				->if($controller->isReadable())
				->and($controller->isNotWritable())
				->and($usePath = null)
					->boolean($controller->stream_open($path = uniqid(), 'r', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'r+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'r+', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', STREAM_REPORT_ERRORS)))->once()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->stream_open($path = uniqid(), 'r', STREAM_USE_PATH, $usePath))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r', STREAM_USE_PATH, null)))->once()
					->string($usePath)->isEqualTo($controller->getPath())
				->if($oldUsePath = $usePath)
				->then
					->boolean($controller->stream_open($path = uniqid(), 'r+', STREAM_USE_PATH, $usePath))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'r+', STREAM_USE_PATH, $oldUsePath)))->once()
					->variable($usePath)->isNull()
			->assert('Use w and w+ mode')
				->if($controller = new testedClass(uniqid()))
				->and($controller->setCalls($calls = new \mock\mageekguy\atoum\test\adapter\calls()))
				->and($usePath = null)
				->then
					->boolean($controller->stream_open($path = uniqid(), 'w', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('a'))->isEqualTo(1)
					->boolean($controller->stream_open($path = uniqid(), 'w+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w+', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('a'))->isEqualTo(1)
					->boolean($controller->stream_open($path = uniqid(), 'w', STREAM_USE_PATH, $usePath))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w', STREAM_USE_PATH, null)))->once()
					->string($usePath)->isEqualTo($controller->getPath())
					->boolean($controller->stream_open($path = uniqid(), 'w+', STREAM_USE_PATH, $usePath))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w+', STREAM_USE_PATH, $usePath)))->once()
					->string($usePath)->isEqualTo($controller->getPath())
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->stream_open($path = uniqid(), 'w', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('a'))->isEqualTo(1)
					->boolean($controller->stream_open($path = uniqid(), 'w+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w+', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('a'))->isEqualTo(1)
				->if($controller->notExists())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'w', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w', 0)))->once()
					->integer($controller->getPermissions())->isEqualTo(644)
				->if($controller->notExists())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'w+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w+', 0)))->once()
					->integer($controller->getPermissions())->isEqualTo(644)
				->if($controller->exists())
				->and($controller->isNotWritable())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'w', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'w', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w', STREAM_REPORT_ERRORS)))->once()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->stream_open($path = uniqid(), 'w+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w+', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'w+', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'w+', STREAM_REPORT_ERRORS)))->once()
					->error('Permission denied', E_USER_WARNING)->exists()
			->assert('Use c and c+ mode')
				->if($controller = new testedClass(uniqid()))
				->and($controller->setCalls($calls = new \mock\mageekguy\atoum\test\adapter\calls()))
				->and($usePath = null)
				->then
					->boolean($controller->stream_open($path = uniqid(), 'c', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'c', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('a'))->isEqualTo(1)
					->boolean($controller->stream_open($path = uniqid(), 'c+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'c+', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEqualTo('a')
					->integer($controller->stream_write('a'))->isEqualTo(1)
					->boolean($controller->stream_open($path = uniqid(), 'c', STREAM_USE_PATH, $usePath))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'c', STREAM_USE_PATH, null)))->once()
					->string($usePath)->isEqualTo($controller->getPath())
					->boolean($controller->stream_open($path = uniqid(), 'c+', STREAM_USE_PATH, $usePath))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'c+', STREAM_USE_PATH, $usePath)))->once()
					->string($usePath)->isEqualTo($controller->getPath())
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->stream_open($path = uniqid(), 'c', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'c', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('a'))->isEqualTo(1)
					->boolean($controller->stream_open($path = uniqid(), 'c+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'c+', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEqualTo('a')
					->integer($controller->stream_write('a'))->isEqualTo(1)
				->if($controller->notExists())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'c', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'c', 0)))->once()
					->integer($controller->getPermissions())->isEqualTo(644)
				->if($controller->notExists())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'c+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'c+', 0)))->once()
					->integer($controller->getPermissions())->isEqualTo(644)
				->if($controller->exists())
				->and($controller->isNotWritable())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'c', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'c', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'c', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->stream_open($path = uniqid(), 'c+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'c+', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'c+', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'c+', STREAM_REPORT_ERRORS)))->once()
					->error('Permission denied', E_USER_WARNING)->exists()
			->assert('Use a and a+ mode')
				->if($controller = new testedClass(uniqid()))
				->and($controller->setCalls($calls = new \mock\mageekguy\atoum\test\adapter\calls()))
				->then
					->boolean($controller->stream_open($path = uniqid(), 'a', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('a'))->isEqualTo(1)
					->string($controller->getContents())->isEqualTo('a')
					->integer($controller->stream_write('b'))->isEqualTo(1)
					->string($controller->getContents())->isEqualTo('ab')
					->boolean($controller->stream_open($path = uniqid(), 'a', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('c'))->isEqualTo(1)
					->string($controller->getContents())->isEqualTo('ab' . PHP_EOL . 'c')
					->integer($controller->stream_write('d'))->isEqualTo(1)
					->string($controller->getContents())->isEqualTo('ab' . PHP_EOL . 'cd')
					->boolean($controller->stream_open($path = uniqid(), 'a+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a+', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEqualTo('a')
					->integer($controller->stream_write('e'))->isEqualTo(1)
					->string($controller->getContents())->isEqualTo('ab' . PHP_EOL . 'cd' . PHP_EOL . 'e')
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->stream_open($path = uniqid(), 'a', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('A'))->isEqualTo(1)
					->string($controller->getContents())->isEqualTo('abcdefghijklmnopqrstuvwxyz' . PHP_EOL . 'A')
					->boolean($controller->stream_open($path = uniqid(), 'a+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a+', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEqualTo('a')
					->integer($controller->stream_write('B'))->isEqualTo(1)
					->string($controller->getContents())->isEqualTo('abcdefghijklmnopqrstuvwxyz' . PHP_EOL . 'A' . PHP_EOL . 'B')
					->integer($controller->stream_write('C'))->isEqualTo(1)
					->string($controller->getContents())->isEqualTo('abcdefghijklmnopqrstuvwxyz' . PHP_EOL . 'A' . PHP_EOL . 'BC')
				->if($controller->notExists())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'a', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a', 0)))->once()
					->integer($controller->getPermissions())->isEqualTo(644)
				->if($controller->notExists())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'a+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a+', 0)))->once()
					->integer($controller->getPermissions())->isEqualTo(644)
				->if($controller->exists())
				->and($controller->isNotWritable())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'a', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->boolean($controller->stream_open($path = uniqid(), 'a+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a+', 0)))->once()
				->if($controller = new testedClass(uniqid()))
				->and($controller->setCalls($calls = new \mock\mageekguy\atoum\test\adapter\calls()))
				->and($controller->isWritable())
				->and($controller->isNotReadable())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'a', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->boolean($controller->stream_open($path = uniqid(), 'a+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a+', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'a+', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'a+', STREAM_REPORT_ERRORS)))->once()
					->error('Permission denied', E_USER_WARNING)->exists()
			->assert('Use x and x+ mode')
				->if($controller = new testedClass(uniqid()))
				->and($controller->setCalls($calls = new \mock\mageekguy\atoum\test\adapter\calls()))
				->then
					->boolean($controller->stream_open($path = uniqid(), 'x', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->boolean($controller->stream_open($path = uniqid(), 'x', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x', STREAM_REPORT_ERRORS)))->once()
					->error('File exists', E_USER_WARNING)->exists()
					->boolean($controller->stream_open($path = uniqid(), 'x+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x+', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'x+', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x+', STREAM_REPORT_ERRORS)))->once()
					->error('File exists', E_USER_WARNING)->exists()
				->if($controller->notExists())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'x', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('a'))->isEqualTo(0)
					->boolean($controller->stream_open($path = uniqid(), 'x+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x+', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEmpty()
					->integer($controller->stream_write('a'))->isEqualTo(1)
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->stream_open($path = uniqid(), 'x', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEqualTo('a')
					->integer($controller->stream_write('a'))->isEqualTo(0)
					->boolean($controller->stream_open($path = uniqid(), 'x+', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x+', 0)))->once()
					->integer($controller->stream_tell())->isZero()
					->string($controller->stream_read(1))->isEqualTo('a')
					->integer($controller->stream_write('a'))->isEqualTo(1)
				->if($controller->isNotReadable())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'x', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'x', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x', STREAM_REPORT_ERRORS)))->once()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->stream_open($path = uniqid(), 'x+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x+', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'x+', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x+', STREAM_REPORT_ERRORS)))->once()
					->error('Permission denied', E_USER_WARNING)->exists()
				->if($controller->isReadable())
				->and($controller->isNotWritable())
				->then
					->boolean($controller->stream_open($path = uniqid(), 'x', 0))->isTrue()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'x+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x+', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'x+', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x+', STREAM_REPORT_ERRORS)))->once()
					->error('Permission denied', E_USER_WARNING)->exists()
				->if($controller->stream_open = false)
				->then
					->boolean($controller->stream_open($path = uniqid(), 'x', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'x+', 0))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x+', 0)))->once()
					->boolean($controller->stream_open($path = uniqid(), 'x+', STREAM_REPORT_ERRORS))->isFalse()
					->mock($calls)->call('addCall')->withArguments(new test\adapter\call('stream_open', array($path, 'x+', STREAM_REPORT_ERRORS)))->once()
					->error('Permission denied', E_USER_WARNING)->notExists()
		;
	}

	public function testStreamSeek()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->stream_seek(0))->isTrue()
				->boolean($controller->stream_seek(1))->isTrue()
			->if($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->and($controller->stream_open(uniqid(), 'r', 0))
			->then
				->boolean($controller->stream_seek(0))->isTrue()
				->boolean($controller->stream_seek(1))->isTrue()
				->string($controller->stream_read(1))->isEqualTo('b')
				->boolean($controller->stream_seek(25))->isTrue()
				->string($controller->stream_read(1))->isEqualTo('z')
				->boolean($controller->stream_seek(26))->isTrue()
				->string($controller->stream_read(1))->isEmpty()
				->boolean($controller->stream_seek(0))->isTrue()
				->string($controller->stream_read(1))->isEqualTo('a')
				->boolean($controller->stream_seek(-1, SEEK_END))->isTrue()
				->string($controller->stream_read(1))->isEqualTo('z')
				->boolean($controller->stream_seek(-26, SEEK_END))->isTrue()
				->string($controller->stream_read(1))->isEqualTo('a')
				->boolean($controller->stream_seek(-27, SEEK_END))->isTrue()
				->string($controller->stream_read(1))->isEmpty()
			->if($controller = new testedClass(uniqid()))
			->and($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->and($controller->stream_open(uniqid(), 'r', 0))
			->and($controller->stream_read(4096))
			->then
				->boolean($controller->stream_eof())->isFalse()
			->if($controller->stream_read(4096))
			->then
				->boolean($controller->stream_eof())->isTrue()
				->boolean($controller->stream_seek(0))->isTrue()
				->boolean($controller->stream_eof())->isFalse()
		;
	}

	public function testStreamEof()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->stream_eof())->isFalse()
			->if($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->boolean($controller->stream_eof())->isFalse()
			->if($controller->stream_seek(26))
			->then
				->boolean($controller->stream_eof())->isFalse()
			->if($controller->stream_seek(27))
			->then
				->boolean($controller->stream_eof())->isFalse()
			->if($controller->stream_open(uniqid(), 'r', 0))
			->and($controller->stream_seek(27))
			->and($controller->stream_read(1))
			->then
				->boolean($controller->stream_eof())->isTrue()
		;
	}

	public function testStreamTell()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->integer($controller->stream_tell())->isZero()
			->if($controller->stream_seek($offset = rand(1, 4096)))
			->then
				->integer($controller->stream_tell())->isEqualTo($offset)
		;
	}

	public function testStreamRead()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->stream_open(uniqid(), 'r', 0))
			->then
				->string($controller->stream_read(1))->isEmpty()
				->boolean($controller->stream_eof())->isTrue()
			->if($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->string($controller->stream_read(1))->isEqualTo('a')
				->boolean($controller->stream_eof())->isFalse()
			->if($controller->stream_seek(6))
			->then
				->string($controller->stream_read(1))->isEqualTo('g')
				->string($controller->stream_read(4096))->isEqualTo('hijklmnopqrstuvwxyz')
				->boolean($controller->stream_eof())->isFalse()
				->string($controller->stream_read(1))->isEmpty()
				->boolean($controller->stream_eof())->isTrue()
		;
	}

	public function testStreamWrite()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->integer($controller->stream_write('a'))->isZero()
				->integer($controller->stream_tell())->isZero()
			->if($controller->stream_open(uniqid(), 'r', 0))
			->then
				->integer($controller->stream_write('a'))->isZero()
				->integer($controller->stream_tell())->isZero()
			->if($controller->stream_open(uniqid(), 'w', 0))
			->then
				->integer($controller->stream_write('a'))->isEqualTo(1)
				->integer($controller->stream_tell())->isEqualTo(1)
				->integer($controller->stream_write('bcdefghijklmnopqrstuvwxyz'))->isEqualTo(25)
				->integer($controller->stream_tell())->isEqualTo(26)
		;
	}

	/** @php 5.4 */
	public function testStreamMetadata()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->stream_metadata(uniqid(), STREAM_META_ACCESS, 755))->isTrue()
				->integer($controller->getPermissions())->isEqualTo(755)
		;
	}

	public function testStreamStat()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($stats = array(
					'dev' => 0,
					'ino' => 0,
					'mode' => 33188,
					'nlink' => 0,
					'uid' => getmyuid(),
					'gid' => getmygid(),
					'rdev' => 0,
					'size' => 0,
					'atime' => 507769200,
					'mtime' => 507769200,
					'ctime' => 507769200,
					'blksize' => 0,
					'blocks' => 0,
				)
			)
			->and($stats[0] = & $stats['dev'])
			->and($stats[1] = & $stats['ino'])
			->and($stats[2] = & $stats['mode'])
			->and($stats[3] = & $stats['nlink'])
			->and($stats[4] = & $stats['uid'])
			->and($stats[5] = & $stats['gid'])
			->and($stats[6] = & $stats['rdev'])
			->and($stats[7] = & $stats['size'])
			->and($stats[8] = & $stats['atime'])
			->and($stats[9] = & $stats['mtime'])
			->and($stats[10] = & $stats['ctime'])
			->and($stats[11] = & $stats['blksize'])
			->and($stats[12] = & $stats['blocks'])
			->then
				->array($controller->stream_stat())->isEqualTo($stats)
			->if($controller->notExists())
			->then
				->boolean($controller->stream_stat())->isFalse()
			->if($controller = new testedClass(uniqid()))
			->and($controller->stream_stat[2] = false)
			->then
				->array($controller->stream_stat())->isNotEmpty()
				->boolean($controller->stream_stat())->isFalse()
				->array($controller->stream_stat())->isNotEmpty()
		;
	}

	public function testStreamTruncate()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->stream_truncate(0))->isTrue()
				->string($controller->getContents())->isEmpty()
				->boolean($controller->stream_truncate($size = rand(1, 10)))->isTrue()
				->string($controller->getContents())->isEqualTo(str_repeat("\0", $size))
				->boolean($controller->stream_truncate(0))->isTrue()
				->string($controller->getContents())->isEmpty()
				->boolean($controller->stream_truncate($size = rand(5, 10)))->isTrue()
				->string($controller->getContents())->isEqualTo(str_repeat("\0", $size))
				->boolean($controller->stream_truncate(4))->isTrue()
				->string($controller->getContents())->isEqualTo("\0\0\0\0")
		;
	}

	public function testStreamLock()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->stream_lock(LOCK_SH))->isTrue()
				->boolean($controller->stream_lock(LOCK_EX))->isTrue()
				->boolean($controller->stream_lock(LOCK_UN))->isTrue()
				->boolean($controller->stream_lock(LOCK_SH | LOCK_NB))->isTrue()
				->boolean($controller->stream_lock(LOCK_EX | LOCK_NB))->isTrue()
			->if($controller->stream_lock = false)
			->then
				->boolean($controller->stream_lock(LOCK_SH))->isFalse()
				->boolean($controller->stream_lock(LOCK_EX))->isFalse()
				->boolean($controller->stream_lock(LOCK_UN))->isFalse()
				->boolean($controller->stream_lock(LOCK_SH | LOCK_NB))->isFalse()
				->boolean($controller->stream_lock(LOCK_EX | LOCK_NB))->isFalse()
		;
	}

	public function testStreamClose()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->stream_close())->isTrue()
			->if($controller->stream_close = false)
			->then
				->boolean($controller->stream_close())->isFalse()
		;
	}

	public function testUrlStat()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($stats = array(
					'dev' => 0,
					'ino' => 0,
					'mode' => 33188,
					'nlink' => 0,
					'uid' => getmyuid(),
					'gid' => getmygid(),
					'rdev' => 0,
					'size' => 0,
					'atime' => 507769200,
					'mtime' => 507769200,
					'ctime' => 507769200,
					'blksize' => 0,
					'blocks' => 0,
				)
			)
			->and($stats[0] = & $stats['dev'])
			->and($stats[1] = & $stats['ino'])
			->and($stats[2] = & $stats['mode'])
			->and($stats[3] = & $stats['nlink'])
			->and($stats[4] = & $stats['uid'])
			->and($stats[5] = & $stats['gid'])
			->and($stats[6] = & $stats['rdev'])
			->and($stats[7] = & $stats['size'])
			->and($stats[8] = & $stats['atime'])
			->and($stats[9] = & $stats['mtime'])
			->and($stats[10] = & $stats['ctime'])
			->and($stats[11] = & $stats['blksize'])
			->and($stats[12] = & $stats['blocks'])
			->then
				->array($controller->url_stat(uniqid(), STREAM_URL_STAT_QUIET))->isEqualTo($stats)
			->if($controller->notExists())
			->then
				->boolean($controller->url_stat(uniqid(), STREAM_URL_STAT_QUIET))->isFalse()
			->if($controller = new testedClass(uniqid()))
			->and($controller->url_stat[2] = false)
			->then
				->array($controller->url_stat(uniqid(), STREAM_URL_STAT_QUIET))->isNotEmpty()
				->boolean($controller->url_stat(uniqid(), STREAM_URL_STAT_QUIET))->isFalse()
				->array($controller->url_stat(uniqid(), STREAM_URL_STAT_QUIET))->isNotEmpty()
		;
	}

	public function testUnlink()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->unlink(uniqid()))->isTrue()
				->boolean($controller->stream_stat())->isFalse()
				->boolean($controller->unlink(uniqid()))->isFalse()
				->boolean($controller->stream_stat())->isFalse()
			->if($controller->exists())
			->then
				->boolean($controller->unlink(uniqid()))->isTrue()
				->boolean($controller->stream_stat())->isFalse()
				->boolean($controller->unlink(uniqid()))->isFalse()
				->boolean($controller->stream_stat())->isFalse()
			->if($controller->exists())
			->and($controller->isNotWritable())
			->then
				->boolean($controller->unlink(uniqid()))->isFalse()
				->array($controller->stream_stat())->isNotEmpty()
			->if($controller->isWritable())
				->boolean($controller->unlink(uniqid()))->isTrue()
				->boolean($controller->stream_stat())->isFalse()
				->boolean($controller->unlink(uniqid()))->isFalse()
				->boolean($controller->stream_stat())->isFalse()
		;
	}

	public function testRename()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->rename(uniqid(), $newPath = uniqid()))->isTrue()
				->string($controller->getPath())->isEqualTo($newPath)
			->if($controller->rename = false)
			->then
				->boolean($controller->rename(uniqid(), uniqid()))->isFalse()
				->string($controller->getPath())->isEqualTo($newPath)
		;
	}

	public function testMkdir()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->mkdir(uniqid(), 0777, STREAM_MKDIR_RECURSIVE))->isFalse()
		;
	}

	public function testRmdir()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->rmdir(uniqid(), STREAM_MKDIR_RECURSIVE))->isFalse()
		;
	}

	public function testDirOpendir()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->dir_opendir(uniqid(), 0x00))->isFalse()
				->boolean($controller->dir_opendir(uniqid(), 0x04))->isFalse()
		;
	}

	public function testDirClosedir()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->dir_closedir())->isFalse()
		;
	}

	public function testDirReaddir()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->dir_readdir())->isFalse()
		;
	}

	public function testDirRewinddir()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->dir_rewinddir())->isFalse()
		;
	}
}
