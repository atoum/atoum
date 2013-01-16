<?php

namespace mageekguy\atoum\tests\units\mock\streams;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\stream,
	mageekguy\atoum\mock\streams\file as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class file extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\mock\stream');
	}

	public function testGet()
	{
		$this
			->if($file = testedClass::get())
			->then
				->object($file)->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->castToString($file)->isNotEmpty()
				->string(file_get_contents($file))->isEmpty()
				->variable($fileResource = fopen($file, 'r'))->isNotEqualTo(false)
				->boolean(is_readable($file))->isTrue()
				->boolean(is_writable($file))->isFalse()
				->boolean(rename($file, testedClass::defaultProtocol . '://' . uniqid()))->isTrue()
				->boolean(fclose($fileResource))->isTrue()
				->boolean(unlink($file))->isTrue()
			->if($file = testedClass::get($path = uniqid()))
			->then
				->object($file)->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->castToString($file)->isEqualTo(testedClass::defaultProtocol . '://' . $path)
				->string(file_get_contents($file))->isEmpty()
				->variable($fileResource = fopen($file, 'r'))->isNotEqualTo(false)
				->boolean(is_readable($file))->isTrue()
				->boolean(is_writable($file))->isFalse()
				->boolean(rename($file, testedClass::defaultProtocol . '://' . uniqid()))->isTrue()
				->boolean(fclose($fileResource))->isTrue()
				->boolean(unlink($file))->isTrue()
		;
	}

	public function testCanNotBeOpened()
	{
		$this
			->if($file = testedClass::get())
			->then
				->object($file->canNotBeOpened())->isIdenticalTo($file)
				->boolean(@fopen($file, 'r'))->isFalse()
		;
	}

	public function testCanBeOpened()
	{
		$this
			->if($file = testedClass::get())
			->and($file->canNotBeOpened())
			->then
				->object($file->canBeOpened())->isIdenticalTo($file)
				->variable(@fopen($file, 'r'))->isNotFalse()
		;
	}

	public function testCanNotBeRead()
	{
		$this
			->if($file = testedClass::get())
			->then
				->object($file->canNotBeRead())->isIdenticalTo($file)
				->boolean(is_readable($file))->isFalse()
		;
	}

	public function testCanBeRead()
	{
		$this
			->if($file = testedClass::get())
			->and($file->canNotBeRead())
			->then
				->object($file->canBeRead())->isIdenticalTo($file)
				->boolean(is_readable($file))->isTrue()
		;
	}

	public function testCanNotBeWrited()
	{
		$this
			->if($file = testedClass::get())
			->then
				->object($file->canNotBeWrited())->isIdenticalTo($file)
				->boolean(is_writable($file))->isFalse()
		;
	}

	public function testCanBeWrited()
	{
		$this
			->if($file = testedClass::get())
			->and($file->canNotBeWrited())
			->then
				->object($file->canBeWrited())->isIdenticalTo($file)
				->boolean(is_writable($file))->isTrue()
		;
	}

	public function testContains()
	{
		$this
			->if($file = testedClass::get())
			->and($file->contains($data = 'abcdefghijklmnopqrstuvwxyz'))
			->and($resource = fopen($file, 'r'))
			->then
				->string(fread($resource, 1))->isEqualTo('a')
				->string(fread($resource, 1))->isEqualTo('b')
				->string(fread($resource, 2))->isEqualTo('cd')
				->string(fread($resource, 8192))->isEqualTo('efghijklmnopqrstuvwxyz')
				->string(fread($resource, 1))->isEmpty()
				->string(file_get_contents($file))->isEqualTo($data)
		;
	}
}
