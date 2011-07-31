<?php

namespace mageekguy\atoum\tests\units\asserters\mock\call;

require_once(__DIR__ . '/../../../../runner.php');

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
		$call = new call\adapter(
				$mockAsserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				$adapter = new test\adapter(),
				$functionName = uniqid()
		);

		$this->assert
			->object($call->getMockAsserter())->isIdenticalTo($mockAsserter)
			->object($call->getAdapter())->isIdenticalTo($adapter)
			->string($call->getFunctionName())->isEqualTo($functionName)
			->variable($call->getArguments())->isNull()
		;

		$call = new call\adapter(
				$mockAsserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				$adapter = new test\adapter,
				$functionName = rand(1, PHP_INT_MAX)
		);

		$this->assert
			->object($call->getMockAsserter())->isIdenticalTo($mockAsserter)
			->object($call->getAdapter())->isIdenticalTo($adapter)
			->string($call->getFunctionName())->isEqualTo((string) $functionName)
			->variable($call->getArguments())->isNull()
		;
	}

	public function test__call()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\asserters\mock')
		;

		$call = new call\adapter(
				$mockAsserter = new \mock\mageekguy\atoum\asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				new test\adapter(),
				uniqid()
		);

		$mockAsserter->getMockController()->call = $mockAsserter;

		$this->assert
			->object($call->call($arg = uniqid()))->isIdenticalTo($mockAsserter)
			->mock($mockAsserter)
				->call('call')->withArguments($arg)->once()
		;

		$unknownFunction = uniqid();

		$this->assert
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
		$call = new call\adapter(
				new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				new test\adapter(),
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
		$call = new call\adapter(
				new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				$adapter = new test\adapter(),
				'md5'
		);

		$this->assert
			->variable($call->getFirstCall())->isNull()
		;

		$otherAdapter = new test\adapter();
		$otherAdapter->md5(uniqid());

		$this->assert
			->variable($call->getFirstCall())->isNull()
		;

		$adapter->md5(uniqid());

		$this->assert
			->integer($call->getFirstCall())->isEqualTo(2)
		;

		$adapter->md5(uniqid());

		$this->assert
			->integer($call->getFirstCall())->isEqualTo(2)
		;
	}

	public function testGetLastCall()
	{
		$call = new call\adapter(
				new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				$adapter = new test\adapter(),
				'md5'
		);

		$this->assert
			->variable($call->getLastCall())->isNull()
		;

		$otherAdapter = new test\adapter();
		$otherAdapter->md5(uniqid());

		$this->assert
			->variable($call->getLastCall())->isNull()
		;

		$adapter->md5(uniqid());

		$this->assert
			->integer($call->getLastCall())->isEqualTo(2)
		;

		$adapter->md5(uniqid());

		$this->assert
			->integer($call->getLastCall())->isEqualTo(3)
		;
	}
}

?>
