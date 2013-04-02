<?php

namespace mageekguy\atoum\tests\units\mock\streams\fs;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\stream,
	mageekguy\atoum\mock\streams\fs\file as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

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
			->if($file = testedClass::get($path = uniqid()))
			->and($otherFile = testedClass::get($path))
			->then
				->object($otherFile)->isIdenticalTo($file)
			->if($resource = fopen($file, 'r'))
			->and($otherResource = fopen($otherFile, 'w'))
			->then
				->integer(fwrite($resource, uniqid()))->isZero()
				->integer(fwrite($otherResource, 'abcdefghijklmnopqrstuvwxyz'))->isEqualTo(26)
		;
	}

	public function testFileSize()
	{
		$this
			->if($file = testedClass::get())
			->then
				->integer(filesize($file))->isEqualTo(0)
			->if($file->contains($data = ('abcdefghijklmnopqrstuvwxyz' . PHP_EOL)))
			->then
				->integer(filesize($file))->isEqualTo(strlen($data))
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

	/** @php 5.4 */
	public function testChmod()
	{
		$this
			->if($file = testedClass::get())
			->and(chmod($file, 755))
			->then
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
				->boolean(fopen($file, 'r'))->isFalse()
				->error->withType(E_WARNING)->exists()
			->if($file->exists())
			->then
				->variable(fopen($file, 'r'))->isNotFalse()
				->variable($resource = fopen($file, 'a'))->isNotFalse()
				->integer(fseek($resource, 0))->isZero()
				->integer(ftell($resource))->isZero()
			->if($file->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->variable($resource = fopen($file, 'a'))->isNotFalse()
				->integer(ftell($resource))->isZero()
				->string(fread($resource, 1))->isEmpty()
				->integer(fseek($resource, 0))->isZero()
				->integer(ftell($resource))->isZero()
				->string(fread($resource, 1))->isEmpty()
				->integer(fwrite($resource, 'A'))->isEqualTo(1)
				->integer(fseek($resource, 0))->isZero()
				->integer(ftell($resource))->isZero()
				->string(fread($resource, 1))->isEmpty()
				->string($file->getContents())->isEqualTo('abcdefghijklmnopqrstuvwxyz' . PHP_EOL . 'A')
			->then
				->variable($resource = fopen($file, 'r'))->isNotFalse()
				->integer(ftell($resource))->isZero()
				->string(fread($resource, 1))->isEqualTo('a')
				->integer(fseek($resource, 0))->isZero()
				->integer(ftell($resource))->isZero()
				->string(fread($resource, 1))->isEqualTo('a')
		;
	}

	public function testFreadAndFileGetContents()
	{
		$this
			->if($file = testedClass::get())
			->and($file->contains($data = 'abcdefghijklmnopqrstuvwxyz' . PHP_EOL))
			->and($resource = fopen($file, 'r'))
			->then
				->string(fread($resource, 1))->isEqualTo('a')
				->string(fread($resource, 1))->isEqualTo('b')
				->string(fread($resource, 2))->isEqualTo('cd')
				->string(fread($resource, 4096))->isEqualTo('efghijklmnopqrstuvwxyz' . PHP_EOL)
				->string(fread($resource, 1))->isEmpty()
				->string(file_get_contents($file))->isEqualTo($data)
				->string(fread($resource, 1))->isEmpty()
			->if(fseek($resource, 0))
			->then
				->string(fread($resource, 1))->isEqualTo('a')
				->string(fread($resource, 1))->isEqualTo('b')
				->string(fread($resource, 2))->isEqualTo('cd')
				->string(file_get_contents($file))->isEqualTo($data)
				->string(fread($resource, 8192))->isEqualTo('efghijklmnopqrstuvwxyz' . PHP_EOL)
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
			->if($file = testedClass::get())
			->and($file->contains(
					($line1 = 'un' . PHP_EOL) .
					($line2 = 'deux' . PHP_EOL) .
					($line3 = 'trois' . PHP_EOL) .
					($line4 = 'quatre' . PHP_EOL) .
					($line5 = 'cinq' . PHP_EOL) .
					PHP_EOL
				)
			)
			->and($resource = fopen($file, 'r'))
			->then
				->boolean(feof($resource))->isFalse()
			->if($line = fgets($resource))
			->then
				->boolean(feof($resource))->isFalse()
				->string($line)->isEqualTo($line1)
			->if($line = fgets($resource))
			->then
				->boolean(feof($resource))->isFalse()
				->string($line)->isEqualTo($line2)
			->if($line = fgets($resource))
			->then
				->boolean(feof($resource))->isFalse()
				->string($line)->isEqualTo($line3)
			->if($line = fgets($resource))
			->then
				->boolean(feof($resource))->isFalse()
				->string($line)->isEqualTo($line4)
			->if($line = fgets($resource))
			->then
				->string($line)->isEqualTo($line5)
				->boolean(feof($resource))->isFalse()
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

	public function testFgets()
	{
		$this
			->if($file = testedClass::get())
			->and($file->contains(
					($line0 = 'un' . PHP_EOL) .
					($line1 = 'deux' . PHP_EOL) .
					($line2 = 'trois' . PHP_EOL) .
					($line3 = 'quatre' . PHP_EOL) .
					($line4 = 'cinq' . PHP_EOL) .
					PHP_EOL
				)
			)
			->and($resource = fopen($file, 'r'))
			->then
				->string(fgets($resource))->isEqualTo($line0)
				->string(fgets($resource))->isEqualTo($line1)
				->string(fgets($resource))->isEqualTo($line2)
				->string(fgets($resource))->isEqualTo($line3)
				->string(fgets($resource))->isEqualTo($line4)
				->string(fgets($resource))->isEqualTo(PHP_EOL)
				->boolean(fgets($resource))->isFalse()
		;
	}

	public function testFseek()
	{
		$this
			->if($file = testedClass::get(uniqid()))
			->and($resource = fopen($file, 'w'))
			->then
				->integer(fseek($resource, 4096))->isZero()
			->if($file = testedClass::get())
			->and($file->contains(
					($line0 = 'un' . PHP_EOL) .
					($line1 = 'deux' . PHP_EOL) .
					($line2 = 'trois' . PHP_EOL) .
					($line3 = 'quatre' . PHP_EOL) .
					($line4 = 'cinq' . PHP_EOL) .
					PHP_EOL
				)
			)
			->and($fileObject = new \splFileObject($file))
			->then
				->boolean($fileObject->eof())->isFalse()
			->if($fileObject->seek(1))
			->then
				->boolean($fileObject->eof())->isFalse()
				->string($fileObject->current())->isEqualTo($line1)
			->if($fileObject->seek(2))
			->then
				->boolean($fileObject->eof())->isFalse()
				->string($fileObject->current())->isEqualTo($line2)
			->if($fileObject->seek(3))
			->then
				->boolean($fileObject->eof())->isFalse()
				->string($fileObject->current())->isEqualTo($line3)
			->if($fileObject->seek(4))
			->then
				->boolean($fileObject->eof())->isFalse()
				->string($fileObject->current())->isEqualTo($line4)
			->if($fileObject->seek(0))
			->then
				->boolean($fileObject->eof())->isFalse()
				->string($fileObject->current())->isEqualTo($line0)
			->if($fileObject->seek(6))
			->then
				->boolean($fileObject->eof())->isTrue()
				->boolean($fileObject->valid())->isFalse()
				->string($fileObject->current())->isEmpty()
			->if($fileObject->seek(5))
			->then
				->boolean($fileObject->eof())->isFalse()
				->string($fileObject->current())->isEqualTo(PHP_EOL)
			->if($fileObject->seek(4))
			->then
				->boolean($fileObject->eof())->isFalse()
				->string($fileObject->current())->isEqualTo($line4)
			->if($fileObject->seek(3))
			->then
				->boolean($fileObject->eof())->isFalse()
				->string($fileObject->current())->isEqualTo($line3)
			->if($fileObject->seek(4))
			->then
				->boolean($fileObject->eof())->isFalse()
				->string($fileObject->current())->isEqualTo($line4)
			->if($fileObject->seek(5))
			->then
				->boolean($fileObject->eof())->isFalse()
				->string($fileObject->current())->isEqualTo(PHP_EOL)
			->if($fileObject = new \splFileObject($file))
			->then
				->integer($fileObject->key())->isZero()
				->string($fileObject->current())->isEqualTo($line0)
				->boolean($fileObject->eof())->isFalse()
			->if($fileObject->next())
			->then
				->integer($fileObject->key())->isEqualTo(1)
				->string($fileObject->current())->isEqualTo($line1)
				->boolean($fileObject->eof())->isFalse()
			->if($fileObject->next())
			->then
				->integer($fileObject->key())->isEqualTo(2)
				->string($fileObject->current())->isEqualTo($line2)
				->boolean($fileObject->eof())->isFalse()
			->if($fileObject->next())
			->then
				->integer($fileObject->key())->isEqualTo(3)
				->string($fileObject->current())->isEqualTo($line3)
				->boolean($fileObject->eof())->isFalse()
			->if($fileObject->next())
			->then
				->integer($fileObject->key())->isEqualTo(4)
				->string($fileObject->current())->isEqualTo($line4)
				->boolean($fileObject->eof())->isFalse()
			->if($fileObject->next())
			->then
				->integer($fileObject->key())->isEqualTo(5)
				->string($fileObject->current())->isEqualTo(PHP_EOL)
				->boolean($fileObject->eof())->isFalse()
			->if($fileObject->next())
			->then
				->integer($fileObject->key())->isEqualTo(6)
				->string($fileObject->current())->isEmpty()
				->boolean($fileObject->eof())->isTrue()
			->if($file = testedClass::get())
			->and($file->contains(
					($line0 = 'un' . PHP_EOL) .
					($line1 = 'deux' . PHP_EOL) .
					($line2 = 'trois' . PHP_EOL) .
					($line3 = 'quatre' . PHP_EOL) .
					($line4 = 'cinq' . PHP_EOL)
				)
			)
			->and($fileObject = new \splFileObject($file))
			->and($fileObject->seek(4))
			->then
				->string($fileObject->current())->isEqualTo($line4)
				->boolean($fileObject->eof())->isFalse()
				->boolean($fileObject->valid())->isTrue()
		;
	}

	/** @php 5.4 */
	public function testFtruncate()
	{
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
			->if($file = testedClass::get($path = uniqid()))
			->then
				->boolean(rename($file, $newPath = testedClass::defaultProtocol . '://' . uniqid()))->isTrue()
				->string($file->getPath())->isEqualTo($newPath)
				->object($file)
					->isIdenticalTo(testedClass::get($newPath))
					->isNotIdenticalTo(testedClass::get($path))
		;
	}

	public function testCopy()
	{
		$this
			->if($file = testedClass::get($path = uniqid()))
			->then
				->boolean(copy($file, $newPath = testedClass::defaultProtocol . '://' . uniqid()))->isTrue()
				->string($file->getPath())->isEqualTo(testedClass::defaultProtocol . '://' . $path)
				->array(stat($file))->isEqualTo(stat(testedClass::get($newPath)))
				->string($file->getContents())->isEqualTo(testedClass::get($newPath)->getContents())
			->if($file->contains(uniqid()))
			->then
				->boolean(copy($file, $otherNewPath = testedClass::defaultProtocol . '://' . uniqid()))->isTrue()
				->string($file->getPath())->isEqualTo(testedClass::defaultProtocol . '://' . $path)
				->string($file->getContents())->isNotEqualTo(testedClass::get($newPath)->getContents())
				->string($file->getContents())->isEqualTo(testedClass::get($otherNewPath)->getContents())
				->array(stat($file))->isNotEqualTo(stat(testedClass::get($newPath)))
				->array(stat($file))->isEqualTo(stat(testedClass::get($otherNewPath)))
		;
	}

	public function testUnlink()
	{
		$this
			->if($file = testedClass::get(uniqid()))
			->then
				->boolean(unlink($file))->isTrue()
				->boolean(is_file($file))->isFalse()
			->if($file = testedClass::get(uniqid()))
			->and($file->notExists())
			->then
				->boolean(unlink($file))->isFalse()
			->if($file->exists())
			->and($file->isNotWritable())
			->then
				->boolean(unlink($file))->isFalse()
			->and($file->isWritable())
			->then
				->boolean(unlink($file))->isTrue()
		;
	}

	public function testOpendir()
	{
		$this
			->if($file = testedClass::get(uniqid()))
			->then
				->boolean(opendir($file))->isFalse()
				->error->withType(E_WARNING)->exists()
		;
	}
}
