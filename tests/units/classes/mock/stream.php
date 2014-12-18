<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum\test,
	mageekguy\atoum\adapter,
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
				->string($streamController->getPath())->matches('#^' . testedClass::defaultProtocol . '://\w+$#')
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
				->string($stream . '\\' . uniqid())->matches('#^' . $stream . preg_quote('\\') . '[^' . preg_quote('\\') . ']+$#')
				->object($subStream = testedClass::getSubStream($stream))->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->castToString($subStream)->matches('#^' . $stream . preg_quote(DIRECTORY_SEPARATOR) . '[^' . preg_quote(DIRECTORY_SEPARATOR) . ']+$#')
				->object($subStream = testedClass::getSubStream($stream, $basename = uniqid()))->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->castToString($subStream)->matches('#^' . $stream . preg_quote(DIRECTORY_SEPARATOR) . $basename . '$#')
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
}
