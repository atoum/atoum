<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum\mock,
	mageekguy\atoum\test,
	mageekguy\atoum\adapter
;

require_once __DIR__ . '/../../runner.php';

class stream extends test
{
	public function testClassConstants()
	{
		$this
			->string(mock\stream::defaultProtocol)->isEqualTo('atoum')
			->string(mock\stream::protocolSeparator)->isEqualTo('://')
		;
	}

	public function testGetAdapter()
	{
		$this
			->object(mock\stream::getAdapter())->isEqualTo(new adapter())
			->if(mock\stream::setAdapter($adapter = new adapter()))
			->then
				->object(mock\stream::getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testGet()
	{
		$this
			->if(mock\stream::setAdapter($adapter = new test\adapter()))
			->and($adapter->stream_get_wrappers = array())
			->and($adapter->stream_wrapper_register = true)
			->then
				->object($streamController = mock\stream::get($stream = uniqid()))->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->string($streamController->getStream())->isEqualTo(mock\stream::defaultProtocol . '://' . mock\stream::setDirectorySeparator($stream))
				->adapter($adapter)
					->call('stream_wrapper_register')->withArguments(mock\stream::defaultProtocol, 'mageekguy\atoum\mock\stream')->once()
			->if($adapter->stream_get_wrappers = array(mock\stream::defaultProtocol))
			->then
				->object($streamController = mock\stream::get())->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->string($streamController->getStream())->match('#^' . mock\stream::defaultProtocol . '://\w+$#')
				->adapter($adapter)
					->call('stream_wrapper_register')->withArguments(mock\stream::defaultProtocol, 'mageekguy\atoum\mock\stream')->once()
				->object(mock\stream::get($stream))->isIdenticalTo($streamController = mock\stream::get($stream))
				->adapter($adapter)
					->call('stream_wrapper_register')->withArguments(mock\stream::defaultProtocol, 'mageekguy\atoum\mock\stream')->once()
				->object(mock\stream::get($otherStream = ($protocol = uniqid()) . '://' . uniqid()))->isNotIdenticalTo($streamController)
				->adapter($adapter)
					->call('stream_wrapper_register')->withArguments($protocol, 'mageekguy\atoum\mock\stream')->once()
			->if($adapter->stream_get_wrappers = array(mock\stream::defaultProtocol, $protocol))
			->then
				->object(mock\stream::get($otherStream))->isIdenticalTo(mock\stream::get($otherStream))
				->object(mock\stream::get($otherStream))->isIdenticalTo(mock\stream::get($otherStream))
				->adapter($adapter)
					->call('stream_wrapper_register')->withArguments($protocol, 'mageekguy\atoum\mock\stream')->once()
			->if($adapter->stream_get_wrappers = array())
			->and($adapter->stream_wrapper_register = false)
			->then
				->exception(function() use ($protocol) { mock\stream::get($protocol . '://' . uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to register ' . $protocol . ' stream')
		;
	}

	public function testGetSubStream()
	{
		$this
			->if(mock\stream::setAdapter($adapter = new test\adapter()))
			->and($adapter->stream_get_wrappers = array())
			->and($adapter->stream_wrapper_register = true)
			->and($stream = mock\stream::get())
			->then
				->object($subStream = mock\stream::getSubStream($stream))->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->castToString($subStream)->match('#^' . $stream . preg_quote(DIRECTORY_SEPARATOR) . '[^' . preg_quote(DIRECTORY_SEPARATOR) . ']+$#')
				->object($subStream = mock\stream::getSubStream($stream, $basename = uniqid()))->isInstanceOf('mageekguy\atoum\mock\stream\controller')
				->castToString($subStream)->match('#^' . $stream . preg_quote(DIRECTORY_SEPARATOR) . $basename . '$#')
		;
	}

	public function testGetProtocol()
	{
		$this
			->variable(mock\stream::getProtocol(uniqid()))->isNull()
			->string(mock\stream::getProtocol(($scheme = uniqid()) . '://' . uniqid()))->isEqualTo($scheme)
		;
	}

	public function testSetDirectorySeparator()
	{
		$this
			->string(mock\stream::setDirectorySeparator('foo/bar', '/'))->isEqualTo('foo/bar')
			->string(mock\stream::setDirectorySeparator('foo\bar', '/'))->isEqualTo('foo/bar')
			->string(mock\stream::setDirectorySeparator('foo/bar', '\\'))->isEqualTo('foo\bar')
			->string(mock\stream::setDirectorySeparator('foo\bar', '\\'))->isEqualTo('foo\bar')
			->string(mock\stream::setDirectorySeparator('foo' . DIRECTORY_SEPARATOR . 'bar'))->isEqualTo('foo' . DIRECTORY_SEPARATOR . 'bar')
			->string(mock\stream::setDirectorySeparator('foo' . (DIRECTORY_SEPARATOR == '/' ? '\\' : '/') . 'bar'))->isEqualTo('foo' . DIRECTORY_SEPARATOR . 'bar')
		;
	}
}
