<?php

namespace mageekguy\atoum\tests\units\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\writers\http as testedClass
;

require_once __DIR__ . '/../../runner.php';

class http extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->isSubclassOf('\\mageekguy\\atoum\\writer')
				->implements('mageekguy\atoum\report\writers\asynchronous')
		;
	}

	public function test__construct()
	{
		$this
			->if($writer = new testedClass())
			->then
				->array($writer->getHeaders())->isEmpty()
				->string($writer->getMethod())->isEqualTo('GET')
				->variable($writer->getParameter())->isNull()
				->variable($writer->getUrl())->isNull()
		;
	}

	public function testWrite()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->file_get_contents = '')
			->and($adapter->stream_context_create = $context = uniqid())
			->and($writer = new testedClass($adapter))
			->then
				->exception(function() use ($writer) {
						$writer->write(uniqid());
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\exceptions\\runtime')
					->hasMessage('No URL set for HTTP writer')
			->if($writer->setUrl($url = uniqid()))
			->then
				->object($writer->write($string = uniqid()))->isIdenticalTo($writer)
				->adapter($adapter)
					->call('stream_context_create')->withArguments(array('http' => array('method'  => 'GET', 'header'  => '', 'content' => $string)))->once()
					->call('file_get_contents')->withArguments($url, false, $context)->once()
			->if($writer->setMethod($method = uniqid()))
			->and($adapter->resetCalls())
			->then
				->object($writer->write($string = uniqid()))->isIdenticalTo($writer)
				->adapter($adapter)
					->call('stream_context_create')->withArguments(array('http' => array('method'  => $method, 'header'  => '', 'content' => $string)))->once()
					->call('file_get_contents')->withArguments($url, false, $context)->once()
			->if($writer->setParameter($param = uniqid()))
			->and($adapter->resetCalls())
			->then
				->object($writer->write($string = uniqid()))->isIdenticalTo($writer)
				->adapter($adapter)
					->call('stream_context_create')->withArguments(array('http' => array('method'  => $method, 'header'  => '', 'content' => http_build_query(array($param => $string)))))->once()
					->call('file_get_contents')->withArguments($url, false, $context)->once()
			->if($writer->addHeader($header = uniqid(), $value = uniqid()))
			->and($adapter->resetCalls())
			->then
				->object($writer->write($string = uniqid()))->isIdenticalTo($writer)
				->adapter($adapter)
					->call('stream_context_create')->withArguments(array('http' => array('method'  => $method, 'header'  => $header . ': ' . $value, 'content' => http_build_query(array($param => $string)))))->once()
					->call('file_get_contents')->withArguments($url, false, $context)->once()
			->if($writer->addHeader($otherHeader = uniqid(), $otherValue = uniqid()))
			->and($adapter->resetCalls())
			->then
				->object($writer->write($string = uniqid()))->isIdenticalTo($writer)
				->adapter($adapter)
					->call('stream_context_create')->withArguments(array('http' => array('method'  => $method, 'header'  => $header . ': ' . $value . "\r\n" . $otherHeader . ': ' . $otherValue, 'content' => http_build_query(array($param => $string)))))->once()
					->call('file_get_contents')->withArguments($url, false, $context)->once()
		;
	}

	public function testClear()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->clear())->isIdenticalTo($writer)
		;
	}

	public function testWriteAsynchronousReport()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->file_get_contents = '')
			->and($report = new \mock\mageekguy\atoum\reports\asynchronous())
			->and($writer = new \mock\mageekguy\atoum\writers\http($adapter))
			->and($writer->setUrl($url = uniqid()))
			->then
				->object($writer->writeAsynchronousReport($report))->isIdenticalTo($writer)
				->mock($writer)->call('write')->withArguments($report->__toString())->once()
			->if($adapter->file_get_contents = false)
			->then
				->exception(function() use ($writer, $report) { $writer->writeAsynchronousReport($report); })
					->isInstanceOf('mageekguy\atoum\writers\http\exception')
					->hasMessage('Unable to write coverage report to ' . $url)
		;
	}

	public function testAddGetHeader()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->addHeader($name = uniqid(), $value = uniqid()))->isIdenticalTo($writer)
				->array($writer->getHeaders())->isEqualTo(array($name => $value))
			->if($writer->addHeader($name, $value = uniqid()))
			->then
				->array($writer->getHeaders())->isEqualTo(array($name => $value))
		;
	}

	public function testGetSetMethod()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->setMethod($method = uniqid()))->isIdenticalTo($writer)
				->string($writer->getMethod())->isEqualTo($method)
		;
	}

	public function testGetSetParameter()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->setParameter($parameter = uniqid()))->isIdenticalTo($writer)
				->string($writer->getParameter())->isEqualTo($parameter)
		;
	}

	public function testGetSetUrl()
	{
		$this
			->if($writer = new testedClass())
			->then
				->object($writer->setUrl($url = uniqid()))->isIdenticalTo($writer)
				->string($writer->getUrl())->isEqualTo($url)
		;
	}
}
