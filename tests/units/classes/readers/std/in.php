<?php

namespace mageekguy\atoum\tests\units\readers\std;

use
	mageekguy\atoum,
	mageekguy\atoum\readers\std\in as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class in extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\reader');
	}

	public function testRead()
	{
		$this
			->given($reader = new testedClass($adapter = new atoum\test\adapter()))
			->if($adapter->fopen = false)
			->then
				->exception(function() use ($reader) { $reader->read(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to open php://stdin stream')
				->adapter($adapter)
					->call('fopen')->withArguments('php://stdin', 'r')->once()
			->if($adapter->fopen = $resource = uniqid())
			->and($adapter->fgets = $line = uniqid())
			->then
				->string($reader->read())->isEqualTo($line)
				->adapter($adapter)
					->call('fopen')->withArguments('php://stdin', 'r')->twice()
					->call('fgets')->withArguments($resource)->once()
				->string($reader->read($length = rand(1, PHP_INT_MAX)))->isEqualTo($line)
				->adapter($adapter)->call('fgets')->withArguments($resource, $length)->once()
		;
	}
}
