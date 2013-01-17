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

	public function testFopen()
	{
		$this
			->if($file = testedClass::get())
			->and($file->canNotBeOpened())
			->then
				->boolean(@fopen($file, 'r'))->isFalse()
			->if($file->canBeOpened())
			->then
				->variable(@fopen($file, 'r'))->isNotFalse()
		;
	}

	public function testIsReadable()
	{
		$this
			->if($file = testedClass::get())
			->and($file->canNotBeRead())
			->then
				->boolean(is_readable($file))->isFalse()
			->if($file->canBeRead())
			->then
				->boolean(is_readable($file))->isTrue()
		;
	}

	public function testIsWritable()
	{
		$this
			->if($file = testedClass::get())
			->and($file->canNotBeWrited())
			->then
				->boolean(is_writable($file))->isFalse()
			->if($file->canBeWrited())
			->then
				->boolean(is_writable($file))->isTrue()
		;
	}

	public function testFreadAndFileGetContents()
	{
		$this
			->if($file = testedClass::get())
			->and($file->contains($data = 'abcdefghijklmnopqrstuvwxyz'))
			->and($resource = fopen($file, 'r'))
			->then
				->string(fread($resource, 1))->isEqualTo('a')
				->string(fread($resource, 1))->isEqualTo('b')
				->string(fread($resource, 2))->isEqualTo('cd')
				->string(fread($resource, 4096))->isEqualTo('efghijklmnopqrstuvwxyz')
				->string(fread($resource, 1))->isEmpty()
				->string(file_get_contents($file))->isEqualTo($data)
				->string(fread($resource, 1))->isEmpty()
			->if(fseek($resource, 0))
			->then
				->string(fread($resource, 1))->isEqualTo('a')
				->string(fread($resource, 1))->isEqualTo('b')
				->string(fread($resource, 2))->isEqualTo('cd')
				->string(fread($resource, 8192))->isEqualTo('efghijklmnopqrstuvwxyz')
				->string(fread($resource, 1))->isEmpty()
				->string(file_get_contents($file))->isEqualTo($data)
				->string(fread($resource, 1))->isEmpty()
			->if($file->isEmpty())
			->and($resource = fopen($file, 'r'))
			->then
				->string(fread($resource, 1))->isEmpty()
				->string(fread($resource, 1))->isEmpty()
				->string(fread($resource, 2))->isEmpty()
				->string(fread($resource, 8192))->isEmpty()
				->string(fread($resource, 1))->isEmpty()
				->string(file_get_contents($file))->isEmpty()
				->string(fread($resource, 1))->isEmpty()
		;
	}

	public function testFeof()
	{
		$this
			->if($file = testedClass::get())
			->and($resource = fopen($file, 'r'))
			->then
				->boolean(feof($resource))->isFalse()
			->if(fread($resource, 1))
			->then
				->boolean(feof($resource))->isTrue()
			->if($file->contains('abcdefghijklmnopqrstuvwxyz'))
			->and($resource = fopen($file, 'r'))
			->then
				->boolean(feof($resource))->isFalse()
			->if(fread($resource, 1))
			->then
				->boolean(feof($resource))->isFalse()
			->if(fread($resource, 4096))
			->then
				->boolean(feof($resource))->isTrue()
		;
	}

	public function testFlock()
	{
		$this
			->if($file = testedClass::get($file = uniqid()))
			->and($resource = fopen($file, 'w'))
			->then
				->boolean(flock($resource, LOCK_EX))->isTrue()
			->if($otherResource = fopen($file, 'w'))
			->then
				->boolean(flock($resource, LOCK_EX))->isFalse()
				->boolean(flock($otherResource, LOCK_EX))->isFalse()
		;
	}
}
