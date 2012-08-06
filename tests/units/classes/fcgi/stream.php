<?php

namespace mageekguy\atoum\tests\units\fcgi;

use
	mageekguy\atoum,
	mageekguy\atoum\fcgi,
	mageekguy\atoum\fcgi\stream as testedClass
;

require_once __DIR__ . '/../../runner.php';

class stream extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($stream = new testedClass())
			->then
				->string($stream->getAddress())->isEqualTo('tcp://127.0.0.1:9000')
				->integer($stream->getTimeout())->isEqualTo(30)
				->boolean($stream->isPersistent())->isFalse()
				->object($stream->getAdapter())->isEqualTo(new atoum\adapter())
				->boolean($stream->isOpen())->isFalse()
			->if($stream = new testedClass($address = 'unix://path/to/unix/socket', $timeout = rand(1, 29), true, $adapter = new atoum\adapter()))
			->then
				->string($stream->getAddress())->isEqualTo($address)
				->integer($stream->getTimeout())->isEqualTo($timeout)
				->boolean($stream->isPersistent())->isTrue()
				->object($stream->getAdapter())->isIdenticalTo($adapter)
				->boolean($stream->isOpen())->isFalse()
		;
	}

	public function test__toString()
	{
		$this
			->if($stream = new testedClass())
			->then
				->castToString($stream)->isEqualTo('tcp://127.0.0.1:9000')
		;
	}

	public function testAdapter()
	{
		$this
			->if($stream = new testedClass())
			->then
				->object($stream->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($stream)
				->object($stream->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testOpen()
	{
		$this
			->if($stream = new testedClass())
			->and($stream->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->fclose = function() {})
			->and($adapter->stream_socket_client = function($socket, & $errno, & $errstr, $timeout, $flags) use (& $errorCode, & $errorMessage) {
					$errorCode = $errno = rand(1, PHP_INT_MAX);
					$errorMessage = $errstr = uniqid();
					return false;
				}
			)
			->then
				->exception(function() use ($stream) { $stream->open(); })
					->isInstanceOf('mageekguy\atoum\fcgi\stream\exception')
					->hasCode($errorCode)
					->hasMessage($errorMessage)
				->boolean($stream->isOpen())->isFalse()
			->if($adapter->stream_socket_client = $socket = uniqid())
			->then
				->object($stream->open())->isIdenticalTo($stream)
				->boolean($stream->isOpen())->isTrue()
		;
	}

	public function testClose()
	{
		$this
			->if($stream = new testedClass())
			->and($stream->setAdapter($adapter = new atoum\test\adapter()))
			->then
				->object($stream->close())->isIdenticalTo($stream)
				->boolean($stream->isOpen())->isFalse()
			->if($adapter->stream_socket_client = $socket = uniqid())
			->and($adapter->fclose = function() {})
			->and($stream->open())
			->then
				->object($stream->close())->isIdenticalTo($stream)
				->boolean($stream->isOpen())->isFalse()
		;
	}

	public function testWrite()
	{
		$this
			->if($stream = new testedClass())
			->and($stream->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->fclose = function() {})
			->and($adapter->stream_socket_client = function($socket, & $errno, & $errstr, $timeout, $flags) use (& $errorCode, & $errorMessage) {
					$errorCode = $errno = rand(1, PHP_INT_MAX);
					$errorMessage = $errstr = uniqid();
					return false;
				}
			)
			->then
				->exception(function() use ($stream) { $stream->write(new fcgi\requests\post()); })
					->isInstanceOf('mageekguy\atoum\fcgi\stream\exception')
					->hasCode($errorCode)
					->hasMessage($errorMessage)
			->if($adapter->resetCalls())
			->and($adapter->stream_socket_client = $socket = uniqid())
			->and($adapter->fwrite = false)
			->then
				->exception(function() use ($stream, & $request) { $stream->write($request = new fcgi\requests\post()); })
					->isInstanceOf('mageekguy\atoum\fcgi\stream\exception')
					->hasMessage('Unable to write data \'' . $request->getStreamData($stream) . '\' in stream \'' . $stream . '\'')
			->if($adapter->resetCalls())
			->and($adapter->fwrite = function() {} )
			->then
				->object($stream->write($request = new fcgi\requests\post()))->isIdenticalTo($stream)
				->adapter($adapter)->call('fwrite')->withArguments($socket, $data = $request->getStreamData($stream), strlen($data))->once()
		;
	}

	public function testRead()
	{
		$this
			->if($stream = new testedClass())
			->then
				->exception(function() use ($stream) { $stream->read(); })
					->isInstanceOf('mageekguy\atoum\fcgi\stream\exception')
					->hasMessage('Stream \'' . $stream . '\' is not open')
			->if($stream->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->stream_socket_client = $socket = uniqid())
			->and($adapter->fclose = function() {})
			->and($adapter->fread = false)
			->and($stream->open())
			->then
				->exception(function() use ($stream) { $stream->read(); })
					->isInstanceOf('mageekguy\atoum\fcgi\stream\exception')
					->hasMessage('Unable to read record from stream \'' . $stream . '\'')
			->if($adapter->resetCalls())
			->and($adapter->fread = $data = uniqid())
			->then
				->array($stream->read())->isEmpty()
				->adapter($adapter)->call('fread')->withArguments($socket, 8)->once()
		;
	}
}
