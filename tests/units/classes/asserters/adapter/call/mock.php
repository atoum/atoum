<?php

namespace atoum\tests\units\asserters\adapter\call;

require_once __DIR__ . '/../../../../runner.php';

use
	atoum,
	atoum\asserter,
	atoum\asserters,
	atoum\asserters\adapter\call
;

class mock extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('atoum\php\call');
	}

	public function test__construct()
	{
		$this
			->if($call = new call\mock($adapterAsserter = new asserters\adapter(new asserter\generator()), $mockAggregator = new \mock\dummy(), $methodName = uniqid()))
			->then
				->object($call->getAdapterAsserter())->isIdenticalTo($adapterAsserter)
				->object($call->getObject())->isIdenticalTo($mockAggregator)
				->string($call->getFunction())->isEqualTo($methodName)
				->variable($call->getArguments())->isNull()
			->if($call = new call\mock($adapterAsserter = new asserters\adapter(new asserter\generator()), $mockAggregator = new \mock\dummy, $methodName = rand(1, PHP_INT_MAX)))
			->then
				->object($call->getAdapterAsserter())->isIdenticalTo($adapterAsserter)
				->object($call->getObject())->isIdenticalTo($mockAggregator)
				->string($call->getFunction())->isEqualTo((string) $methodName)
				->variable($call->getArguments())->isNull()
		;
	}

	public function test__call()
	{
		$this
			->if($call = new call\mock($adapterAsserter = new \mock\atoum\asserters\adapter(new asserter\generator()), new \mock\dummy(), uniqid()))
			->and($adapterAsserter->getMockController()->call = $adapterAsserter)
			->then
				->object($call->call($arg = uniqid()))->isIdenticalTo($adapterAsserter)
				->mock($adapterAsserter)
					->call('call')->withArguments($arg)->once()
			->if($unknownMethod = uniqid())
			->then
				->exception(function() use ($call, $unknownMethod) { $call->{$unknownMethod}(); })
					->isInstanceOf('atoum\exceptions\logic\invalidArgument')
					->hasMessage('Method ' . get_class($adapterAsserter) . '::' . $unknownMethod . '() does not exist')
		;
	}

	public function test__toString()
	{
		$this
			->if($call = new call\mock(new \mock\atoum\asserters\adapter(new asserter\generator()), $mockAggregator = new \mock\dummy(), $function = uniqid()))
			->then
				->castToString($call)->isEqualTo(get_class($mockAggregator) . '::' . $function . '()')
		;
	}

	public function testWithArguments()
	{
		$this
			->if($call = new call\mock(new asserters\adapter(new asserter\generator()), new \mock\dummy(), uniqid()))
			->then
				->object($call->withArguments($arg = uniqid()))->isIdenticalTo($call)
				->array($call->getArguments())->isEqualTo(array($arg))
				->object($call->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($call)
				->array($call->getArguments())->isEqualTo(array($arg1, $arg2))
		;
	}

	public function testGetFirstCall()
	{
		$this
			->if($call = new call\mock(new asserters\adapter(new asserter\generator()), $mock = new \mock\dummy(), 'foo'))
			->then
				->variable($call->getFirstCall())->isNull()
				->when(function() { $otherMock = new \mock\dummy(); $otherMock->foo(); })
					->variable($call->getFirstCall())->isNull()
				->when(function() use ($mock) { $mock->foo(); })
					->integer($call->getFirstCall())->isEqualTo(2)
				->when(function() use ($mock) { $mock->foo(); })
					->integer($call->getFirstCall())->isEqualTo(2)
		;
	}

	public function testGetLastCall()
	{
		$this
			->if($call = new call\mock(new asserters\adapter(new asserter\generator()), $mock = new \mock\dummy(), 'foo'))
			->then
				->variable($call->getLastCall())->isNull()
				->when(function() { $otherMock = new \mock\dummy(); $otherMock->foo(); })
					->variable($call->getLastCall())->isNull()
				->when(function() use ($mock) { $mock->foo(); })
					->integer($call->getLastCall())->isEqualTo(2)
				->when(function() use ($mock) { $mock->foo(); })
					->integer($call->getLastCall())->isEqualTo(3)
		;
	}
}
