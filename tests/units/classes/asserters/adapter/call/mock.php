<?php

namespace mageekguy\atoum\tests\units\asserters\adapter\call;

require_once(__DIR__ . '/../../../../runner.php');

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\asserters\adapter\call
;

class mock extends atoum\test
{
	public function beforeTestMethod($testMethod)
	{
		$this->mockGenerator
			->generate('dummy')
		;
	}

	public function test__construct()
	{
		$call = new call\mock(
				$adapterAsserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score()))),
				$mockAggregator = new \mock\dummy(),
				$methodName = uniqid()
		);

		$this->assert
			->object($call->getAdapterAsserter())->isIdenticalTo($adapterAsserter)
			->object($call->getMockAggregator())->isIdenticalTo($mockAggregator)
			->string($call->getMethodName())->isEqualTo($methodName)
			->variable($call->getArguments())->isNull()
		;

		$call = new call\mock(
				$adapterAsserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score()))),
				$mockAggregator = new \mock\dummy,
				$methodName = rand(1, PHP_INT_MAX)
		);

		$this->assert
			->object($call->getAdapterAsserter())->isIdenticalTo($adapterAsserter)
			->object($call->getMockAggregator())->isIdenticalTo($mockAggregator)
			->string($call->getMethodName())->isEqualTo((string) $methodName)
			->variable($call->getArguments())->isNull()
		;
	}

	public function test__call()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\asserters\adapter')
		;

		$call = new call\mock(
				$adapterAsserter = new \mock\mageekguy\atoum\asserters\adapter(new asserter\generator($test = new self($score = new atoum\score()))),
				new \mock\dummy(),
				uniqid()
		);

		$adapterAsserter->getMockController()->call = $adapterAsserter;

		$this->assert
			->object($call->call($arg = uniqid()))->isIdenticalTo($adapterAsserter)
			->mock($adapterAsserter)
				->call('call')->withArguments($arg)->once()
		;

		$unknownMethod = uniqid();

		$this->assert
			->exception(function() use ($call, $unknownMethod) {
						$call->{$unknownMethod}();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Method ' . get_class($adapterAsserter) . '::' . $unknownMethod . '() does not exist')
		;
	}

	public function testWithArguments()
	{
		$call = new call\mock(
				new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score()))),
				new \mock\dummy(),
				uniqid()
		);

		$this->assert
			->object($call->withArguments($arg = uniqid()))->isIdenticalTo($call)
			->array($call->getArguments())->isEqualTo(array($arg))
			->object($call->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($call)
			->array($call->getArguments())->isEqualTo(array($arg1, $arg2))
		;
	}

	public function testGetFirstCall()
	{
		$call = new call\mock(
				new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score()))),
				$mock = new \mock\dummy(),
				'foo'
		);

		$this->assert
			->variable($call->getFirstCall())->isNull()
		;

		$otherMock = new \mock\dummy();
		$otherMock->foo();

		$this->assert
			->variable($call->getFirstCall())->isNull()
		;

		$mock->foo();

		$this->assert
			->integer($call->getFirstCall())->isEqualTo(2)
		;

		$mock->foo();

		$this->assert
			->integer($call->getFirstCall())->isEqualTo(2)
		;
	}

	public function testGetLastCall()
	{
		$call = new call\mock(
				new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score()))),
				$mock = new \mock\dummy(),
				'foo'
		);

		$this->assert
			->variable($call->getLastCall())->isNull()
		;

		$otherMock = new \mock\dummy();
		$otherMock->foo();

		$this->assert
			->variable($call->getLastCall())->isNull()
			->when(function() use ($mock) { $mock->foo(); })
			->integer($call->getLastCall())->isEqualTo(2)
		;

		$mock->foo();

		$this->assert
			->integer($call->getLastCall())->isEqualTo(3)
		;
	}
}

?>
