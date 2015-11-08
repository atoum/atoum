<?php

namespace mageekguy\atoum\tests\units\test\data;

use mageekguy\atoum;
use mageekguy\atoum\test\data\provider;

require_once __DIR__ . '/../../../runner.php';

class set extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->extends('mageekguy\atoum\test\data\provider\aggregator');
	}

	public function test__construct(provider $provider)
	{
		$this
			->if($this->newTestedInstance($provider))
			->then
				->sizeOf($this->testedInstance)->isEqualTo(1)
			->given($size = rand(1, PHP_INT_MAX))
			->if($this->newTestedInstance($provider, $size))
			->then
				->sizeOf($this->testedInstance)->isEqualTo($size)
		;
	}

	public function test__invoke(provider $provider)
	{
		$this
			->if(
				$set = new \mock\mageekguy\atoum\test\data\set($provider),
				$this->calling($set)->generate->doesNothing
			)
			->when($set())
			->then
				->mock($set)
					->call('generate')->withoutAnyArgument->once
			->given($size = 10)
			->if(
				$set = new \mock\mageekguy\atoum\test\data\set($provider, $size),
				$this->calling($set)->generate->doesNothing
			)
			->when($set())
			->then
				->mock($set)
					->call('generate')->withoutAnyArgument->once
		;
	}

	public function test__toString(provider $provider)
	{
		$this
			->given(
				$string = uniqid(),
				$this->calling($provider)->__toString = $string
			)
			->if($this->newTestedInstance($provider))
			->then
				->castToString($this->testedInstance)->isEqualTo($string)
				->mock($provider)
					->call('__toString')->withoutAnyArgument->once
		;
	}
	public function testGenerate(provider $provider)
	{
		$this
			->if($this->newTestedInstance($provider))
			->then
				->array(call_user_func($this->testedInstance))->hasSize(1)
				->mock($provider)
					->call('generate')->withoutAnyArgument->once
			->given(
				$this->resetMock($provider),
				$size = 10
			)
			->if($this->newTestedInstance($provider, $size))
			->then
				->array(call_user_func($this->testedInstance))->hasSize($size)
				->mock($provider)
					->call('generate')->withoutAnyArgument->exactly($size)
		;
	}
}
