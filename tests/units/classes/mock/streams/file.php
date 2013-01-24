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
				->boolean(is_writable($file))->isTrue()
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
				->boolean(is_writable($file))->isTrue()
				->boolean(rename($file, testedClass::defaultProtocol . '://' . uniqid()))->isTrue()
				->boolean(fclose($fileResource))->isTrue()
				->boolean(unlink($file))->isTrue()
		;
	}

	public function testFileSize()
	{
		$this
			->if($file = testedClass::get())
			->then
				->integer(filesize($file))->isEqualTo(0)
			->if($file->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->integer(filesize($file))->isEqualTo(27)
		;
	}

	public function testFilePerms()
	{
		$this
			->if($file = testedClass::get())
			->then
				->integer(fileperms($file))->isEqualTo(0100644)
		;
	}

	public function testChmod()
	{
		if (PHP_VERSION_ID < 50400)
		{
			$this->skip('It\'s not possible to use chmod() on a stream before PHP 5.4.0');
		}

		$this
			->if($file = testedClass::get())
			->and(chmod($file, 755))
			->then
				->dump($file->getMode())
				->integer(fileperms($file))->isEqualTo(0100755)
		;
	}

	public function testFileType()
	{
		$this
			->if($file = testedClass::get())
			->then
				->string(filetype($file))->isEqualTo('file')
		;
	}

	public function testFileOwner()
	{
		$this
			->if($file = testedClass::get())
			->then
				->integer(fileowner($file))->isEqualTo(getmyuid())
		;
	}

	public function testFileGroup()
	{
		$this
			->if($file = testedClass::get())
			->then
				->integer(filegroup($file))->isEqualTo(getmygid())
		;
	}

	public function testIsFile()
	{
		$this
			->if($file = testedClass::get())
			->then
				->boolean(is_file($file))->isTrue()
			->if($file->notExists())
			->then
				->boolean(is_file($file))->isFalse()
		;
	}

	public function testIsDir()
	{
		$this
			->if($file = testedClass::get())
			->then
				->boolean(is_dir($file))->isFalse()
		;
	}

	public function testIsLink()
	{
		$this
			->if($file = testedClass::get())
			->then
				->boolean(is_link($file))->isFalse()
		;
	}

	public function testFileExists()
	{
		$this
			->if($file = testedClass::get())
			->then
				->boolean(file_exists($file))->isTrue()
		;
	}

	public function testIsReadable()
	{
		$this
			->if($file = testedClass::get())
			->and($file->isNotReadable())
			->then
				->boolean(is_readable($file))->isFalse()
			->if($file->isReadable())
			->then
				->boolean(is_readable($file))->isTrue()
		;
	}

	public function testIsWritable()
	{
		$this
			->if($file = testedClass::get())
			->and($file->isNotWritable())
			->then
				->boolean(is_writable($file))->isFalse()
			->if($file->isWritable())
			->then
				->boolean(is_writable($file))->isTrue()
		;
	}

	public function testIsExecutable()
	{
		$this
			->if($file = testedClass::get())
			->then
				->boolean(is_executable($file))->isFalse()
		;
	}

	public function testFopen()
	{
		$this
			->if($file = testedClass::get())
			->and($file->notExists())
			->then
				->boolean(@fopen($file, 'r'))->isFalse()
			->if($file->exists())
			->then
				->variable(@fopen($file, 'r'))->isNotFalse()
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
				->string(file_get_contents($file))->isEqualTo($data)
				->string(fread($resource, 8192))->isEqualTo('efghijklmnopqrstuvwxyz')
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
			->if($file = testedClass::get(uniqid()))
			->and($resource = fopen($file, 'w'))
			->then
				->boolean(flock($resource, LOCK_EX))->isTrue()
				->boolean(flock($resource, LOCK_EX|LOCK_NB))->isTrue()
				->boolean(flock($resource, LOCK_SH))->isTrue()
				->boolean(flock($resource, LOCK_SH|LOCK_NB))->isTrue()
				->boolean(flock($resource, LOCK_UN))->isTrue()
		;
	}

	public function testFtell()
	{
		$this
			->if($file = testedClass::get(uniqid()))
			->and($resource = fopen($file, 'w'))
			->then
				->integer(ftell($resource))->isZero()
			->if(fseek($resource, $offset = rand(1, 4096)))
			->then
				->integer(ftell($resource))->isEqualTo($offset)
				->boolean(feof($resource))->isFalse()
		;
	}

	public function testFseek()
	{
		$this
			->if($file = testedClass::get(uniqid()))
			->and($resource = fopen($file, 'w'))
			->then
				->integer(fseek($resource, 4096))->isZero()
		;
	}

	public function testFtruncate()
	{
		if (PHP_VERSION_ID < 50400)
		{
			$this->skip('It\'s not possible to truncate a stream before PHP 5.4.0, see https://bugs.php.net/bug.php?id=53888');
		}

		$this
			->if($file = testedClass::get(uniqid()))
			->and($resource = fopen($file, 'w'))
			->then
				->boolean(ftruncate($resource, 0))->isTrue()
				->string(file_get_contents($file))->isEmpty()
			->if($file->contains($data = 'abcdefghijklmnopqrstuvwxyz'))
			->then
				->boolean(ftruncate($resource, 4))->isTrue()
				->string(file_get_contents($file))->isEqualTo('abcd')
				->boolean(ftruncate($resource, 8))->isTrue()
				->string(file_get_contents($file))->isEqualTo('abcd' . "\0\0\0\0")
				->boolean(ftruncate($resource, 0))->isTrue()
				->string(file_get_contents($file))->isEmpty()
		;
	}

	public function testFwriteAndFilePutContents()
	{
		$this
			->if($file = testedClass::get())
			->and($resource = fopen($file, 'r'))
			->then
				->integer(fwrite($resource, 'a'))->isZero()
			->if($resource = fopen($file, 'w'))
			->then
				->integer(fwrite($resource, 'a'))->isEqualTo(1)
				->string(file_get_contents($file))->isEqualTo('a')
		;
	}

	public function testRename()
	{
		$this
			->if($file = testedClass::get(uniqid()))
			->then
				->boolean(rename($file, $nePath = testedClass::defaultProtocol . '://' . uniqid()))->isTrue()
		;
	}
}
