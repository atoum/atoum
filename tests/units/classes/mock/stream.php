<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum\test,
	mageekguy\atoum\adapter,
	mageekguy\atoum\mock,
	mageekguy\atoum\mock\stream as testedClass
;

require_once __DIR__ . '/../../runner.php';

class stream extends test
{
	public function testClassConstants()
	{
		$this
			->string(testedClass::defaultProtocol)->isEqualTo('atoum')
			->string(testedClass::protocolSeparator)->isEqualTo('://')
		;
	}

	public function testGetAdapter()
	{
		$this
			->object(testedClass::getAdapter())->isEqualTo(new adapter())
			->if(testedClass::setAdapter($adapter = new adapter()))
			->then
				->object(testedClass::getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testGet()
	{
		$this
			->if(testedClass::setAdapter($adapter = new test\adapter()))
			->and($adapter->stream_get_wrappers = array())
			->and($adapter->stream_wrapper_register = true)
			->then
				->object($streamController = testedClass::get($stream = uniqid()))->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->string($streamController->getPath())->isEqualTo(testedClass::defaultProtocol . '://' . testedClass::setDirectorySeparator($stream))
				->adapter($adapter)
					->call('stream_wrapper_register')->withArguments(testedClass::defaultProtocol, 'mageekguy\atoum\mock\stream')->once()
			->if($adapter->stream_get_wrappers = array(testedClass::defaultProtocol))
			->then
				->object($streamController = testedClass::get())->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->string($streamController->getPath())->match('#^' . testedClass::defaultProtocol . '://\w+$#')
				->adapter($adapter)
					->call('stream_wrapper_register')->withArguments(testedClass::defaultProtocol, 'mageekguy\atoum\mock\stream')->once()
				->object(testedClass::get($stream))->isIdenticalTo($streamController = testedClass::get($stream))
				->adapter($adapter)
					->call('stream_wrapper_register')->withArguments(testedClass::defaultProtocol, 'mageekguy\atoum\mock\stream')->once()
				->object(testedClass::get($otherStream = ($protocol = uniqid()) . '://' . uniqid()))->isNotIdenticalTo($streamController)
				->adapter($adapter)
					->call('stream_wrapper_register')->withArguments($protocol, 'mageekguy\atoum\mock\stream')->once()
			->if($adapter->stream_get_wrappers = array(testedClass::defaultProtocol, $protocol))
			->then
				->object(testedClass::get($otherStream))->isIdenticalTo(testedClass::get($otherStream))
				->object(testedClass::get($otherStream))->isIdenticalTo(testedClass::get($otherStream))
				->adapter($adapter)
					->call('stream_wrapper_register')->withArguments($protocol, 'mageekguy\atoum\mock\stream')->once()
			->if($adapter->stream_get_wrappers = array())
			->and($adapter->stream_wrapper_register = false)
			->then
				->exception(function() use ($protocol) { testedClass::get($protocol . '://' . uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to register ' . $protocol . ' stream')
		;
	}

	public function testGetSubStream()
	{
		$this
			->if(testedClass::setAdapter($adapter = new test\adapter()))
			->and($adapter->stream_get_wrappers = array())
			->and($adapter->stream_wrapper_register = true)
			->and($stream = testedClass::get())
			->then
				->string($stream . '\\' . uniqid())->match('#^' . $stream . preg_quote('\\') . '[^' . preg_quote('\\') . ']+$#')
				->object($subStream = testedClass::getSubStream($stream))->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->castToString($subStream)->match('#^' . $stream . preg_quote(DIRECTORY_SEPARATOR) . '[^' . preg_quote(DIRECTORY_SEPARATOR) . ']+$#')
				->object($subStream = testedClass::getSubStream($stream, $basename = uniqid()))->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->castToString($subStream)->match('#^' . $stream . preg_quote(DIRECTORY_SEPARATOR) . $basename . '$#')
		;
	}

	public function testGetProtocol()
	{
		$this
			->variable(testedClass::getProtocol(uniqid()))->isNull()
			->string(testedClass::getProtocol(($scheme = uniqid()) . '://' . uniqid()))->isEqualTo($scheme)
		;
	}

	public function testSetDirectorySeparator()
	{
		$this
			->string(testedClass::setDirectorySeparator('foo/bar', '/'))->isEqualTo('foo/bar')
			->string(testedClass::setDirectorySeparator('foo\bar', '/'))->isEqualTo('foo/bar')
			->string(testedClass::setDirectorySeparator('foo/bar', '\\'))->isEqualTo('foo\bar')
			->string(testedClass::setDirectorySeparator('foo\bar', '\\'))->isEqualTo('foo\bar')
			->string(testedClass::setDirectorySeparator('foo' . DIRECTORY_SEPARATOR . 'bar'))->isEqualTo('foo' . DIRECTORY_SEPARATOR . 'bar')
			->string(testedClass::setDirectorySeparator('foo' . (DIRECTORY_SEPARATOR == '/' ? '\\' : '/') . 'bar'))->isEqualTo('foo' . DIRECTORY_SEPARATOR . 'bar')
		;
	}

	public function testMkdir()
	{
		$this
			->if($object = new mock\stream())
			->and($filesystem = mock\stream::get())
			->and($directory = $filesystem . DIRECTORY_SEPARATOR . uniqid())
			->then
				->boolean($object->mkdir($directory, 0777, 0))->isTrue()
				->boolean(is_dir($directory))->isTrue()
				->boolean(is_file($directory))->isFalse()
			->if($otherDirectory = $filesystem . DIRECTORY_SEPARATOR . uniqid())
			->and(mock\stream::get($otherDirectory))
			->then
				->exception(function() use ($object, $otherDirectory) {
						$object->mkdir($otherDirectory, 0777, 0);
					})
					->isInstanceOf('\\mageekguy\\atoum\\exceptions\\logic')
					->hasMessage('Stream \'' . $otherDirectory . '\' already exists')
			->if($rootDir = 'atoum://' . uniqid())
			->then
				->boolean($object->mkdir($rootDir, 0777, 0))->isTrue()
				->boolean(is_dir($rootDir))->isTrue()
				->boolean(is_file($rootDir))->isFalse()
		;
	}

	public function testRmdir()
	{
		$this
			->if($object = new mock\stream())
			->and($filesystem = mock\stream::get())
			->then
				->boolean($object->rmdir($filesystem, 0))
				->exception(function() use ($object, $filesystem) {
						$object->rmdir($filesystem, 0);
					})
					->isInstanceOf('\\mageekguy\\atoum\\exceptions\\logic')
					->hasMessage('Stream \'' . $filesystem . '\' is undefined')
		;
	}

	public function testUnlink()
	{
		$this
			->if($object = new mock\stream())
			->and($filesystem = mock\stream::get())
			->if($file = $filesystem . DIRECTORY_SEPARATOR . uniqid())
			->then
				->exception(function() use($file) {
						unlink($file);
					})
					->isInstanceOf('\\mageekguy\\atoum\\exceptions\\logic')
					->hasMessage('Stream \'' . $file . '\' is undefined')
			->if(mock\stream::get($file))
			->then
				->boolean($object->unlink($file))->isTrue()
		;
	}
}
