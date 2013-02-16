<?php

namespace mageekguy\atoum\tests\units\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\writers\file as testedClass
;

require_once __DIR__ . '/../../runner.php';

class file extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->implements('mageekguy\atoum\report\writers\realtime')
				->implements('mageekguy\atoum\report\writers\asynchronous')
		;
	}

	public function testClassConstants()
	{
		$this
			->string(testedClass::defaultFileName)->isEqualTo('atoum.log')
		;
	}

	public function test__construct()
	{
		$this
			->if($file = new testedClass())
			->then
				->object($file->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->string($file->getFilename())->isEqualTo('atoum.log')
			->if($file = new testedClass(null, $adapter = new atoum\adapter()))
			->then
				->object($file->getAdapter())->isIdenticalTo($adapter)
				->string($file->getFilename())->isEqualTo('atoum.log')
			->if($file = new testedClass($filename = uniqid()))
			->then
				->string($file->getFilename())->isEqualTo($filename)
		;
	}

	public function test__destruct()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->fopen = $resource = uniqid())
			->and($adapter->fwrite = function($resource, $data) { return strlen($data); })
			->and($adapter->fflush = function() {})
			->and($adapter->fclose = function() {})
			->and($file = new testedClass(null, $adapter))
			->when(function() use ($file) { $file->__destruct(); })
			->then
				->adapter($adapter)
					->call('fclose')->never()
			->if($file = new testedClass(null, $adapter))
			->and($file->write('something'))
			->when(function() use ($file) { $file->__destruct(); })
			->then
				->adapter($adapter)
					->call('fclose')->withArguments($resource)->once()
		;
	}

	public function testWrite()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->if($adapter->fopen = false)
			->and($file = new testedClass(null, $adapter))
			->and($adapter->resetCalls())
			->then
				->exception(function() use ($file) { $file->write(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to open file \'' . $file->getFilename() . '\'')
			->if($adapter->fopen = $resource = uniqid())
			->and($adapter->fclose = function() {})
			->and($adapter->fwrite = false)
			->and($adapter->fflush = function() {})
			->then
				->exception(function() use ($file) { $file->write(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to write in file \'' . $file->getFilename() . '\'')
			->if($adapter->fwrite = function($resource, $data) { return strlen($data); })
			->and($file = new testedClass(null, $adapter))
			->and($adapter->resetCalls())
			->then
				->object($file->write($string = uniqid()))->isIdenticalTo($file)
				->adapter($adapter)
					->call('fopen')->withArguments($file->getFilename(), 'w')->once()
					->call('fwrite')->withArguments($resource, $string)->once()
					->call('fflush')->withArguments($resource)->once()
				->object($file->write($string = (uniqid() . "\n")))->isIdenticalTo($file)
				->adapter($adapter)
					->call('fopen')->withArguments($file->getFilename(), 'w')->once()
					->call('fwrite')->withArguments($resource, $string)->once()
					->call('fflush')->withArguments($resource)->twice()
		;
	}

	public function testClear()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->if($adapter->fopen = false)
			->and($file = new testedClass(null, $adapter))
			->and($adapter->resetCalls())
			->then
				->exception(function() use ($file) { $file->clear(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to open file \'' . $file->getFilename() . '\'')
			->if($adapter->fopen = $resource = uniqid())
			->and($adapter->ftruncate = false)
			->and($adapter->fclose = function() {})
			->and($adapter->resetCalls())
			->then
				->exception(function() use ($file) { $file->clear(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to truncate file \'' . $file->getFilename() . '\'')
				->adapter($adapter)
					->call('fopen')->withArguments($file->getFilename(), 'w')->once()
			->if($adapter->ftruncate = true)
				->object($file->clear())->isIdenticalTo($file)
				->adapter($adapter)
					->call('fopen')->withArguments($file->getFilename(), 'w')->once()
		;
	}

	public function testSetFilename()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->fopen = $resource = uniqid())
			->and($adapter->fclose = function() {})
			->and($adapter->fwrite = function() {})
			->and($adapter->fflush = function() {})
			->and($file = new testedClass(null, $adapter))
			->then
				->object($file->setFilename($filename = uniqid()))->isIdenticalTo($file)
				->string($file->getFilename())->isEqualTo($filename)
			->if($adapter->fwrite = function($resource, $data) { return strlen($data); })
			->and($obj = $file->write($string = uniqid()))
			->and($file->setFilename('anotherNameAgain'))
			->then
				->string($file->getFilename())->isEqualTo('anotherNameAgain')
				->adapter($adapter)
					->call('fclose')->withArguments($resource)->once()
		;
	}
}
