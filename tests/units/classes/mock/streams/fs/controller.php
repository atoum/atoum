<?php

namespace mageekguy\atoum\tests\units\mock\streams\fs;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\dependence,
	mageekguy\atoum\dependencies,
	mageekguy\atoum\mock\stream,
	mock\mageekguy\atoum\mock\streams\fs\controller as testedClass
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
				->integer($controller->getPermissions())->isZero()
				->object($controller->getAdapter())->isEqualTo(new atoum\adapter())
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($controller)
				->object($controller->getAdapter())->isIdenticalTo($adapter)
				->object($controller->setAdapter())->isIdenticalTo($controller)
				->object($controller->getAdapter())
					->isEqualTo(new atoum\adapter())
					->isNotIdenticalTo($controller)
		;
	}

	public function testGetPermissions()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->isReadable())
			->then
				->integer($controller->getPermissions())->isEqualTo(444)
			->if($controller->notExists())
			->then
				->variable($controller->getPermissions())->isNull()
		;
	}

	public function testNotExists()
	{
		$this
			->if($controller = new testedClass(uniqid(), $adapter = new atoum\test\adapter()))
			->then
				->object($controller->notExists())->isIdenticalTo($controller)
				->variable($controller->getPermissions())->isNull()
				->adapter($adapter)
					->call('clearstatcache')->withArguments(false, $controller->getPath())->once()
		;
	}

	public function testExists()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->notExists())
			->and($controller->setAdapter($adapter = new atoum\test\adapter()))
			->then
				->object($controller->exists())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isZero()
				->adapter($adapter)
					->call('clearstatcache')->withArguments(false, $controller->getPath())->once()
		;
	}

	public function testIsReadable()
	{
		$this
			->if($controller = new testedClass(uniqid(), $adapter = new atoum\test\adapter()))
			->then
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(444)
				->adapter($adapter)
					->call('clearstatcache')->withArguments(false, $controller->getPath())->once()
		;
	}

	public function testIsNotReadable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->isReadable())
			->and($controller->setAdapter($adapter = new atoum\test\adapter()))
			->then
				->object($controller->isNotReadable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isZero()
				->adapter($adapter)
					->call('clearstatcache')->withArguments(false, $controller->getPath())->once()
		;
	}

	public function testIsWritable()
	{
		$this
			->if($controller = new testedClass(uniqid(), $adapter = new atoum\test\adapter()))
			->then
				->object($controller->isWritable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(222)
				->adapter($adapter)
					->call('clearstatcache')->withArguments(false, $controller->getPath())->once()
		;
	}

	public function testIsNotWritable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->isWritable())
			->and($controller->setAdapter($adapter = new atoum\test\adapter()))
			->then
				->object($controller->isNotWritable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isZero()
				->adapter($adapter)
					->call('clearstatcache')->withArguments(false, $controller->getPath())->once()
		;
	}

	public function testIsExecutable()
	{
		$this
			->if($controller = new testedClass(uniqid(), $adapter = new atoum\test\adapter()))
			->then
				->object($controller->isExecutable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isEqualTo(111)
				->adapter($adapter)
					->call('clearstatcache')->withArguments(false, $controller->getPath())->once()
		;
	}

	public function testIsNotExecutable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->isExecutable())
			->and($controller->setAdapter($adapter = new atoum\test\adapter()))
			->then
				->object($controller->isNotExecutable())->isIdenticalTo($controller)
				->integer($controller->getPermissions())->isZero()
				->adapter($adapter)
					->call('clearstatcache')->withArguments(false, $controller->getPath())->once()
		;
	}
}
