<?php

namespace mageekguy\atoum\tests\units\asserters\mock\call;

require_once __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\asserters\mock\call
;

class dummy
{
	public function foo() {}
}

class mock extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($call = new call\mock(
					$mockAsserter = new asserters\mock(new asserter\generator()),
					$mockAggregator = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
					$function = uniqid()
				)
			)
			->then
				->object($call->getMockAsserter())->isIdenticalTo($mockAsserter)
				->object($call->getObject())->isIdenticalTo($mockAggregator)
				->string($call->getFunction())->isEqualTo($function)
				->variable($call->getArguments())->isNull()
		;
	}

	public function test__call()
	{
		$this
			->if($call = new call\mock(
					$mockAsserter = new \mock\mageekguy\atoum\asserters\mock(new asserter\generator()),
					new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
					uniqid()
				)
			)
			->and($mockAsserter->getMockController()->call = $mockAsserter)
			->then
				->object($call->call($arg = uniqid()))->isIdenticalTo($mockAsserter)
				->mock($mockAsserter)
					->call('call')->withArguments($arg)->once()
			->if($unknownMethod = uniqid())
			->then
				->exception(function() use ($call, $unknownMethod) {
							$call->{$unknownMethod}();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Method ' . get_class($mockAsserter) . '::' . $unknownMethod . '() does not exist')
		;
	}

	public function testWithArguments()
	{
		$this
			->if($call = new call\mock(
					new asserters\mock(new asserter\generator()),
					new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
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

	public function testOn()
	{
		$this
			->if($call = new call\mock(
					new asserters\mock(new asserter\generator()),
					new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
					uniqid()
				)
			)
			->and($mockAggregator = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy())
			->then
				->object($call->on($mockAggregator))->isIdenticalTo($call)
				->object($call->getObject())->isIdenticalTo($mockAggregator)
		;
	}

	public function testGetFirstCall()
	{
		$this
			->if($call = new call\mock(
					new asserters\mock(new asserter\generator()),
					$mock = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
					'foo'
				)
			)
			->then
				->variable($call->getFirstCall())->isNull()
			->if($otherMock = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy())
			->and($otherMock->foo())
			->then
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
			->if($call = new call\mock(
					new asserters\mock(new asserter\generator()),
					$mock = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
					'foo'
				)
			)
			->then
				->variable($call->getLastCall())->isNull()
			->if($otherMock = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy())
			->and($otherMock->foo())
			->then
				->variable($call->getLastCall())->isNull()
				->when(function() use ($mock) { $mock->foo(); })
					->integer($call->getLastCall())->isEqualTo(2)
				->when(function() use ($mock) { $mock->foo(); })
					->integer($call->getLastCall())->isEqualTo(3)
		;
	}
}
