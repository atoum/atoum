<?php

namespace mageekguy\atoum\tests\units\asserters\adapter\call;

require_once __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\asserters\adapter\call
;

class adapter extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\php\call');
	}

	public function test__construct()
	{
		$this
			->if($call = new call\adapter($callAsserter = new asserters\call\adapter(new asserters\adapter()), $adapter = new test\adapter(), $function = uniqid()))
			->then
				->object($call->getCallAsserter())->isIdenticalTo($callAsserter)
				->object($call->getAdapter())->isIdenticalTo($adapter)
				->string($call->getFunction())->isEqualTo($function)
				->variable($call->getArguments())->isNull()
			->if($call = new call\adapter($callAsserter = new asserters\call\adapter(new asserters\adapter()), $adapter = new test\adapter, $function = rand(1, PHP_INT_MAX)))
			->then
				->object($call->getCallAsserter())->isIdenticalTo($callAsserter)
				->object($call->getAdapter())->isIdenticalTo($adapter)
				->string($call->getFunction())->isEqualTo((string) $function)
				->variable($call->getArguments())->isNull()
		;
	}

	public function test__call()
	{
		$this
			->if($call = new call\adapter($callAsserter = new \mock\mageekguy\atoum\asserters\call\adapter(new asserters\adapter()),  new test\adapter(), uniqid()))
			->and($callAsserter->getMockController()->beforeFunctionCall = $callAsserter)
			->then
				->object($call->beforeFunctionCall($arg = uniqid(), new test\adapter()))->isIdenticalTo($callAsserter)
				->mock($callAsserter)
					->call('beforeFunctionCall')->withArguments($arg)->once()
			->if($unknownFunction = uniqid())
			->then
				->exception(function() use ($call, $unknownFunction) { $call->{$unknownFunction}(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $unknownFunction . '\' does not exist')
		;
	}

	public function test__toString()
	{
		$this
			->if($call = new call\adapter(new asserters\call\adapter(new asserters\adapter(new asserter\generator())), new test\adapter(), $function = uniqid()))
			->then
				->castToString($call)->isEqualTo($function . '()')
		;
	}

	public function testWithArguments()
	{
		$this
			->if($call = new call\adapter(new asserters\call\adapter(new asserters\adapter(new asserter\generator())), new test\adapter(), uniqid()))
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
			->if($call = new call\adapter(new asserters\call\adapter(new asserters\adapter(new asserter\generator())), $adapter = new test\adapter(), 'md5'))
			->then
				->variable($call->getFirstCall())->isNull()
			->if($otherAdapter = new test\adapter())
			->and($otherAdapter->md5(uniqid()))
			->then
				->variable($call->getFirstCall())->isNull()
			->if($adapter->md5(uniqid()))
			->then
				->integer($call->getFirstCall())->isEqualTo(2)
			->if($adapter->md5(uniqid()))
			->then
				->integer($call->getFirstCall())->isEqualTo(2)
		;
	}

	public function testGetLastCall()
	{
		$this
			->if($call = new call\adapter(new asserters\call\adapter(new asserters\adapter(new asserter\generator())), $adapter = new test\adapter(), 'md5'))
			->then
				->variable($call->getLastCall())->isNull()
			->if($otherAdapter = new test\adapter())
			->and($otherAdapter->md5(uniqid()))
			->then
				->variable($call->getLastCall())->isNull()
			->if($adapter->md5(uniqid()))
			->then
				->integer($call->getLastCall())->isEqualTo(2)
			->if($adapter->md5(uniqid()))
			->then
				->integer($call->getLastCall())->isEqualTo(3)
		;
	}
}
