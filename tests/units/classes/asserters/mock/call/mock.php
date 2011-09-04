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
		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\mock\call\dummy')
		;

		$call = new call\mock(
				$mockAsserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				$mockAggregator = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
				$function = uniqid()
		);

		$this->assert
			->object($call->getMockAsserter())->isIdenticalTo($mockAsserter)
			->object($call->getObject())->isIdenticalTo($mockAggregator)
			->string($call->getFunction())->isEqualTo($function)
			->variable($call->getArguments())->isNull()
		;
	}

	public function test__call()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\asserters\mock')
			->generate('mageekguy\atoum\tests\units\asserters\mock\call\dummy')
		;

		$call = new call\mock(
				$mockAsserter = new \mock\mageekguy\atoum\asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
				uniqid()
		);

		$mockAsserter->getMockController()->call = $mockAsserter;

		$this->assert
			->object($call->call($arg = uniqid()))->isIdenticalTo($mockAsserter)
			->mock($mockAsserter)
				->call('call')->withArguments($arg)->once()
		;

		$unknownMethod = uniqid();

		$this->assert
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
		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\mock\call\dummy')
		;

		$call = new call\mock(
				new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
				uniqid()
		);

		$this->assert
			->object($call->withArguments($arg = uniqid()))->isIdenticalTo($call)
			->array($call->getArguments())->isEqualTo(array($arg))
			->object($call->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($call)
			->array($call->getArguments())->isEqualTo(array($arg1, $arg2))
		;
	}

	public function testOn()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\mock\call\dummy')
		;

		$call = new call\mock(
				new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
				uniqid()
		);

		$mockAggregator = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy();

		$this->assert
			->object($call->on($mockAggregator))->isIdenticalTo($call)
			->object($call->getObject())->isIdenticalTo($mockAggregator)
		;
	}

	public function testGetFirstCall()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\mock\call\dummy')
		;

		$call = new call\mock(
				new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				$mock = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
				'foo'
		);

		$this->assert
			->variable($call->getFirstCall())->isNull()
		;

		$otherMock = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy();
		$otherMock->foo();

		$this->assert
			->variable($call->getFirstCall())->isNull()
			->when(function() use ($mock) { $mock->foo(); })
				->integer($call->getFirstCall())->isEqualTo(2)
			->when(function() use ($mock) { $mock->foo(); })
				->integer($call->getFirstCall())->isEqualTo(2)
		;
	}

	public function testGetLastCall()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\mock\call\dummy')
		;

		$call = new call\mock(
				new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				$mock = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy(),
				'foo'
		);

		$this->assert
			->variable($call->getLastCall())->isNull()
		;

		$otherMock = new \mock\mageekguy\atoum\tests\units\asserters\mock\call\dummy();
		$otherMock->foo();

		$this->assert
			->variable($call->getLastCall())->isNull()
			->when(function() use ($mock) { $mock->foo(); })
				->integer($call->getLastCall())->isEqualTo(2)
			->when(function() use ($mock) { $mock->foo(); })
				->integer($call->getLastCall())->isEqualTo(3)
		;
	}
}

?>
