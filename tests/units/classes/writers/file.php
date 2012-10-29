<?php

namespace mageekguy\atoum\tests\units\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\writers
;

require_once __DIR__ . '/../../runner.php';

class file extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->implements('mageekguy\atoum\adapter\aggregator')
				->implements('mageekguy\atoum\report\writers\realtime')
				->implements('mageekguy\atoum\report\writers\asynchronous')
		;
	}

	public function test__construct()
	{
		$this
			->if($file = new writers\file())
			->then
				->object($file->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->string($file->getFilename())->isEqualTo('atoum.log')
			->if($adapter = new atoum\test\adapter())
			->and($adapter->fopen = function() {})
			->and($adapter->fclose = function() {})
			->and($file = new writers\file(null, $adapter))
			->then
				->object($file->getAdapter())->isIdenticalTo($adapter)
				->string($file->getFilename())->isEqualTo('atoum.log')
			->if($file = new writers\file('test.log'))
			->then
				->string($file->getFilename())->isEqualTo('test.log')
		;
	}

	public function testClassConstants()
	{
		$this
			->string(writers\file::defaultFileName)->isEqualTo('atoum.log')
		;
	}

	public function test__destruct()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->fopen = $handle = uniqid())
			->and($adapter->fwrite = function() {})
			->and($adapter->fclose = function() {})
			->and($adapter->is_writable = function() { return true; })
			->and($file = new writers\file(null, $adapter))
			->and($file->write('something'))
			->then
				->when(function() use ($file) { $file->__destruct(); })
					->adapter($adapter)
						->call('fclose')->withArguments($handle)->once()
		;
	}

	public function testWrite()
	{
		$this
			->if($handle = uniqid())
			->and($adapter = new atoum\test\adapter())
			->and($adapter->fopen = function() use ($handle) { return $handle; })
			->and($adapter->fclose = function() {})
			->and($adapter->fwrite = function() {})
			->and($adapter->is_writable = function() { return true; })
			->and($file = new writers\file(null, $adapter))
			->and($adapter->resetCalls())
			->then
				->object($file->write($string = uniqid()))->isIdenticalTo($file)
				->adapter($adapter)
					->call('dirname')->withArguments('atoum.log')->once()
					->call('is_writable')->withArguments('.')->once()
					->call('fopen')->withArguments('atoum.log', 'w')->once()
					->call('fwrite')->withArguments($handle, $string)->once()
				->object($file->write($string = (uniqid() . "\n")))->isIdenticalTo($file)
				->adapter($adapter)
					->call('fwrite')->withArguments($handle, $string)->once()
			->if($adapter->is_null = function() { return false; })
			->then
				->object($file->write($string = uniqid()))->isIdenticalTo($file)
				->adapter($adapter)
					->call('fwrite')->withArguments($handle, $string)->once()
		;
	}

	public function testSetFilename()
	{
		$this
			->if($handle = uniqid())
			->and($adapter = new atoum\test\adapter())
			->and($adapter->fopen = function() use ($handle) { return $handle; })
			->and($adapter->fclose = function() {})
			->and($adapter->fwrite = function() {})
			->and($adapter->is_writable = function() { return true; })
			->and($adapter->is_null = function() { return true; })
			->and($file = new writers\file(null,$adapter))
			->then
				->string($file->getFilename())->isEqualTo('atoum.log')
			->if($file->setFilename('anotherName'))
			->then
				->string($file->getFilename())->isEqualTo('anotherName')
			->if($adapter->is_null = function() { return false; })
			->and($obj = $file->write($string = uniqid()))
			->and($file->setFilename('anotherNameAgain'))
			->then
				->string($file->getFilename())->isEqualTo('anotherName')
		;
	}

	public function testGetFilename()
	{
		$this
			->if($file = new writers\file())
			->then
				->string($file->getFilename())->isEqualTo('atoum.log')
			->if($file->setFilename('anotherName'))
			->then
				->string($file->getFilename())->isEqualTo('anotherName')
		;
	}
}
