<?php

namespace mageekguy\atoum\tests\units\asserters\adapter\call;

require_once __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\asserters\adapter\call
;

class adapter extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($call = new call\adapter($adapterAsserter = new asserters\adapter(new asserter\generator()), $adapter = new test\adapter(), $functionName = uniqid()))
			->then
				->object($call->getMockAsserter())->isIdenticalTo($adapterAsserter)
				->object($call->getAdapter())->isIdenticalTo($adapter)
				->string($call->getFunctionName())->isEqualTo($functionName)
				->variable($call->getArguments())->isNull()
			->if($call = new call\adapter($adapterAsserter = new asserters\adapter(new asserter\generator()), $adapter = new test\adapter, $functionName = rand(1, PHP_INT_MAX)))
			->then
				->object($call->getMockAsserter())->isIdenticalTo($adapterAsserter)
				->object($call->getAdapter())->isIdenticalTo($adapter)
				->string($call->getFunctionName())->isEqualTo((string) $functionName)
				->variable($call->getArguments())->isNull()
		;
	}

	public function test__call()
	{
		$this
			->if($call = new call\adapter($adapterAsserter = new \mock\mageekguy\atoum\asserters\adapter(new asserter\generator()), new test\adapter(), uniqid()))
			->and($adapterAsserter->getMockController()->call = $adapterAsserter)
			->then
				->object($call->call($arg = uniqid()))->isIdenticalTo($adapterAsserter)
				->mock($adapterAsserter)
					->call('call')->withArguments($arg)->once()
			->if($unknownFunction = uniqid())
			->then
				->exception(function() use ($call, $unknownFunction) { $call->{$unknownFunction}(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Method ' . get_class($adapterAsserter) . '::' . $unknownFunction . '() does not exist')
		;
	}

	public function testWithArguments()
	{
		$this
			->if($call = new call\adapter(new asserters\adapter(new asserter\generator()), new test\adapter(), uniqid()))
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
			->if($call = new call\adapter( new asserters\adapter(new asserter\generator()), $adapter = new test\adapter(), 'md5'))
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
			->if($call = new call\adapter(new asserters\adapter(new asserter\generator()), $adapter = new test\adapter(), 'md5'))
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
