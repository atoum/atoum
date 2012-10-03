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
				->hasInterface('mageekguy\atoum\adapter\aggregator')
				->hasInterface('mageekguy\atoum\report\writers\realtime')
				->hasInterface('mageekguy\atoum\report\writers\asynchronous')
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
		$adapter = new atoum\test\adapter();
		$adapter->fopen = $handle = uniqid();
		$adapter->fwrite = function() {};
		$adapter->fclose = function() {};
		$adapter->is_writable = function() { return true; };

		$file = new writers\file(null, $adapter);

		$file->write('something');

		$this
			->when(function() use ($file) { $file->__destruct(); })
				->adapter($adapter)
					->call('fclose')->withArguments($handle)->once()
		;
	}

	public function testWrite()
	{
		$handle = uniqid();

		$adapter = new atoum\test\adapter();
		$adapter->fopen = function() use ($handle) { return $handle; };
		$adapter->fclose = function() {};
		$adapter->fwrite = function() {};
		$adapter->is_writable = function() { return true; };

		$this
			->if($file = new writers\file(null, $adapter))
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
		$handle = uniqid();

		$adapter = new atoum\test\adapter();
		$adapter->fopen = function() use ($handle) { return $handle; };
		$adapter->fclose = function() {};
		$adapter->fwrite = function() {};
		$adapter->is_writable = function() { return true; };
		$adapter->is_null = function() { return true; };

		$this
			->if($file = new writers\file(null,$adapter))
			->then
				->string($file->getFilename())->isEqualTo('atoum.log')
			->if($file->setFilename('anotherName'))
			->then
				->string($file->getFilename())->isEqualTo('anotherName')
			->if($adapter->is_null = function() { return false; })
			->and($file->write($string = uniqid()))
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
