<?php

namespace mageekguy\atoum\tests\units\asserters\mock\call;

require_once __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\asserters\mock\call
;

class adapter extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($call = new call\adapter(
					$mockAsserter = new asserters\mock(new asserter\generator()),
					$adapter = new test\adapter(),
					$function = uniqid()
				)
			)
			->then
				->object($call->getMockAsserter())->isIdenticalTo($mockAsserter)
				->object($call->getAdapter())->isIdenticalTo($adapter)
				->string($call->getFunction())->isEqualTo($function)
				->variable($call->getArguments())->isNull()
		;
	}

	public function test__call()
	{
		$this
			->if($call = new call\adapter(
					$mockAsserter = new \mock\mageekguy\atoum\asserters\mock(new asserter\generator()),
					new test\adapter(),
					uniqid()
				)
			)
			->and($mockAsserter->getMockController()->call = $mockAsserter)
			->then
				->object($call->call($arg = uniqid()))->isIdenticalTo($mockAsserter)
				->mock($mockAsserter)
					->call('call')->withArguments($arg)->once()
			->if($unknownFunction = uniqid())
			->then
				->exception(function() use ($call, $unknownFunction) {
							$call->{$unknownFunction}();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Method ' . get_class($mockAsserter) . '::' . $unknownFunction . '() does not exist')
		;
	}

	public function testWithArguments()
	{
		$this
			->if($call = new call\adapter(
					new asserters\mock(new asserter\generator()),
					new test\adapter(),
					uniqid()
				)
			)
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
			->if($call = new call\adapter(
					new asserters\mock(new asserter\generator()),
					$adapter = new test\adapter(),
					'md5'
				)
			)
			->then
				->variable($call->getFirstCall())->isNull()
				->when(function() { $otherAdapter = new test\adapter(); $otherAdapter->md5(uniqid()); })
					->variable($call->getFirstCall())->isNull()
				->when(function() use ($adapter) { $adapter->md5(uniqid()); })
					->integer($call->getFirstCall())->isEqualTo(2)
				->when(function() use ($adapter) { $adapter->md5(uniqid()); })
					->integer($call->getFirstCall())->isEqualTo(2)
		;
	}

	public function testGetLastCall()
	{
		$this
			->if($call = new call\adapter(
					new asserters\mock(new asserter\generator()),
					$adapter = new test\adapter(),
					'md5'
				)
			)
			->then
				->variable($call->getLastCall())->isNull()
				->when(function() { $otherAdapter = new test\adapter(); $otherAdapter->md5(uniqid()); })
					->variable($call->getLastCall())->isNull()
				->when(function() use ($adapter) { $adapter->md5(uniqid()); })
					->integer($call->getLastCall())->isEqualTo(2)
				->when(function() use ($adapter) { $adapter->md5(uniqid()); })
					->integer($call->getLastCall())->isEqualTo(3)
		;
	}
}
