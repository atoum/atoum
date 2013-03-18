<?php

namespace mageekguy\atoum\tests\units\mock\streams\fs\directory;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\streams\fs\directory\controller as testedClass
;

require_once __DIR__ . '/../../../../../runner.php';

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
				->integer($controller->getPermissions())->isEqualTo(755)
				->array($controller->getContents())->isEmpty()
		;
	}

	public function testDirOpendir()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->dir_opendir(uniqid(), 0x00))->isTrue()
				->boolean($controller->dir_opendir(uniqid(), 0x04))->isTrue()
			->if($controller->notExists())
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
				->boolean($controller->dir_closedir())->isTrue()
				->boolean($controller->dir_closedir())->isTrue()
			->if($controller->notExists())
			->then
				->boolean($controller->dir_closedir())->isFalse()
				->boolean($controller->dir_closedir())->isFalse()
		;
	}

	public function testMkdir()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->mkdir(uniqid(), 0777, STREAM_MKDIR_RECURSIVE))->isFalse()
			->if($controller->notExists())
			->then
				->boolean($controller->mkdir(uniqid(), 0777, STREAM_MKDIR_RECURSIVE))->isTrue()
				->integer($controller->getPermissions())->isEqualTo(0777)
		;
	}
}
