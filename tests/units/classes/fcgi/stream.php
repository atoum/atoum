<?php

namespace mageekguy\atoum\tests\units\fcgi;

use
	mageekguy\atoum,
	mageekguy\atoum\fcgi,
	mageekguy\atoum\fcgi\records\responses,
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

	public function test__invoke()
	{
		$this
			->if($stream = new testedClass())
			->and($request = new fcgi\request())
			->and($request->STDIN = uniqid())
			->and($stream->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->stream_socket_client = $socket = uniqid())
			->and($adapter->stream_select = 1)
			->and($adapter->socket_set_blocking = function() {})
			->and($adapter->fwrite = strlen($request->getRecords($stream)->getStreamData()))
			->and($adapter->fread = '')
			->and($adapter->fclose = function() {})
			->and($streamData = $request->getRecords($stream)->getStreamData())
			->then
				->object($stream($request))->isIdenticalTo($stream)
				->adapter($adapter)->call('fwrite')->withArguments($socket, $streamData, strlen($streamData))->once()
			->if($stream->setPersistent())
			->and($streamData = $request->getRecords($stream)->getStreamData())
			->then
				->object($stream($request))->isIdenticalTo($stream)
				->adapter($adapter)->call('fwrite')->withArguments($socket, $streamData, strlen($streamData))->once()
			->if($stream = new testedClass())
			->and($stream->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->stream_socket_client = uniqid())
			->and($adapter->stream_select = 1)
			->and($adapter->socket_set_blocking = function() {})
			->and($request = new fcgi\request())
			->and($request->STDIN = uniqid())
			->and($adapter->fwrite = strlen($request->getRecords($stream)->getStreamData()))
			->and($adapter->fclose = function() {})
			->and($adapter->fread[1] = '')
			->and($adapter->fread[2] = "\001\003\000\001\000" . chr(8) . "\000\000\001")
			->and($adapter->fread[3] = "\000\000\000\000\000\000\000\000")
			->and($stream($request))
			->then
				->array($stream())->isEqualTo(array(new fcgi\response($request)))
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($stream = new testedClass())
			->then
				->object($stream->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($stream)
				->object($stream->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetPersistent()
	{
		$this
			->if($stream = new testedClass())
			->then
				->object($stream->setPersistent())->isIdenticalTo($stream)
				->boolean($stream->isPersistent())->isTrue()
			->if($stream = new testedClass())
			->and($stream->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->stream_socket_client = uniqid())
			->and($adapter->stream_select = 1)
			->and($adapter->socket_set_blocking = function() {})
			->and($adapter->fclose = function() {})
			->and($stream->open())
			->then
				->object($stream->setPersistent())->isIdenticalTo($stream)
				->boolean($stream->isPersistent())->isTrue()
				->boolean($stream->isOpen())->isFalse()
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
			->and($adapter->socket_set_blocking = function() {})
			->then
				->exception(function() use ($stream) { $stream->open(); })
					->isInstanceOf('mageekguy\atoum\fcgi\stream\exception')
					->hasCode($errorCode)
					->hasMessage('Unable to connect to \'' . $stream . '\': ' . $errorMessage)
				->boolean($stream->isOpen())->isFalse()
			->if($adapter->stream_socket_client = $socket = uniqid())
			->then
				->object($stream->open())->isIdenticalTo($stream)
				->boolean($stream->isOpen())->isTrue()
				->adapter($adapter)->call('stream_socket_client')->withArguments((string) $stream, null, null, 30, STREAM_CLIENT_CONNECT)->once()
			->if($adapter->resetCalls())
			->and($stream->setPersistent())
			->then
				->object($stream->open())->isIdenticalTo($stream)
				->boolean($stream->isOpen())->isTrue()
				->adapter($adapter)->call('stream_socket_client')->withArguments((string) $stream, null, null, 30, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT)->once()
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
			->and($adapter->socket_set_blocking = true)
			->and($stream->open())
			->then
				->object($stream->close())->isIdenticalTo($stream)
				->boolean($stream->isOpen())->isFalse()
		;
	}

	public function testGenerateRequestId()
	{
		$this
			->if($stream = new testedClass())
			->then
				->string($stream->generateRequestId())->isEqualTo(1)
			->if($request = new fcgi\request())
			->and($request->STDIN = uniqid())
			->and($stream->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->stream_socket_client = uniqid())
			->and($adapter->stream_select = 1)
			->and($adapter->socket_set_blocking = function() {})
			->and($adapter->fwrite = strlen($request->getRecords($stream)->getStreamData()))
			->and($adapter->fread = '')
			->and($adapter->fclose = function() {})
			->and($stream->write($request))
			->then
				->string($stream->generateRequestId())->isEqualTo(2)
			->if($adapter->resetCalls())
			->and($adapter->fread[1] = "\001\003\000\001\000" . chr(8) . "\000\000\001")
			->and($adapter->fread[2] = "\000\000\000\000\000\000\000\000")
			->and($stream->read())
			->then
				->string($stream->generateRequestId())->isEqualTo(1)
		;
	}

	public function testWrite()
	{
		$this
			->if($stream = new testedClass())
			->and($request = new fcgi\request())
			->and($request->STDIN = uniqid())
			->and($stream->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->stream_socket_client = $socket = uniqid())
			->and($adapter->stream_select = 1)
			->and($adapter->socket_set_blocking = function() {})
			->and($adapter->fwrite = strlen($request->getRecords($stream)->getStreamData()))
			->and($adapter->fread = '')
			->and($adapter->fclose = function() {})
			->and($streamData = $request->getRecords($stream)->getStreamData())
			->then
				->object($stream->write($request))->isIdenticalTo($stream)
				->adapter($adapter)->call('fwrite')->withArguments($socket, $streamData, strlen($streamData))->once()
			->if($stream->setPersistent())
			->and($streamData = $request->getRecords($stream)->getStreamData())
			->then
				->object($stream->write($request))->isIdenticalTo($stream)
				->adapter($adapter)->call('fwrite')->withArguments($socket, $streamData, strlen($streamData))->once()
		;
	}

	public function testRead()
	{
		$this
			->if($stream = new testedClass())
			->and($stream->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->stream_socket_client = uniqid())
			->and($adapter->stream_select = 1)
			->and($adapter->socket_set_blocking = function() {})
			->and($request = new fcgi\request())
			->and($request->STDIN = uniqid())
			->and($adapter->fwrite = strlen($request->getRecords($stream)->getStreamData()))
			->and($adapter->fclose = function() {})
			->and($adapter->fread[1] = '')
			->and($adapter->fread[2] = "\001\003\000\001\000" . chr(8) . "\000\000\001")
			->and($adapter->fread[3] = "\000\000\000\000\000\000\000\000")
			->and($stream->write($request))
			->then
				->array($stream->read())->isEqualTo(array(new fcgi\response($request)))
		;
	}
}
