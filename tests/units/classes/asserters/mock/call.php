<?php

namespace mageekguy\atoum\tests\units\asserters\mock;

require_once(__DIR__ . '/../../../runner.php');

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

class dummy {}

class call extends atoum\test
{
	public function test__construct()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\mock\dummy')
		;

		$call = new asserters\mock\call(
				$mockAsserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				$mockAggregator = new \mock\mageekguy\atoum\tests\units\asserters\mock\dummy(),
				$methodName = uniqid()
		);

		$this->assert
			->object($call->getMockAsserter())->isIdenticalTo($mockAsserter)
			->object($call->getMockAggregator())->isIdenticalTo($mockAggregator)
			->string($call->getMethodName())->isEqualTo($methodName)
			->variable($call->getArguments())->isNull()
		;

		$call = new asserters\mock\call(
				$mockAsserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				$mockAggregator = new \mock\mageekguy\atoum\tests\units\asserters\mock\dummy(),
				$methodName = uniqid()
		);

		$this->assert
			->object($call->getMockAsserter())->isIdenticalTo($mockAsserter)
			->object($call->getMockAggregator())->isIdenticalTo($mockAggregator)
			->string($call->getMethodName())->isEqualTo((string) $methodName)
			->variable($call->getArguments())->isNull()
		;
	}

	public function test__call()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\asserters\mock')
			->generate('mageekguy\atoum\tests\units\asserters\mock\dummy')
		;

		$call = new asserters\mock\call(
				$mockAsserter = new \mock\mageekguy\atoum\asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				new \mock\mageekguy\atoum\tests\units\asserters\mock\dummy(),
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
			->generate('mageekguy\atoum\tests\units\asserters\mock\dummy')
		;

		$call = new asserters\mock\call(
				new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				new \mock\mageekguy\atoum\tests\units\asserters\mock\dummy(),
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
			->generate('mageekguy\atoum\tests\units\asserters\mock\dummy')
		;

		$call = new asserters\mock\call(
				new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				new \mock\mageekguy\atoum\tests\units\asserters\mock\dummy(),
				uniqid()
		);

		$mockAggregator = new \mock\mageekguy\atoum\tests\units\asserters\mock\dummy();

		$this->assert
			->object($call->on($mockAggregator))->isIdenticalTo($call)
			->object($call->getMockAggregator())->isIdenticalTo($mockAggregator)
		;
	}
}

?>
