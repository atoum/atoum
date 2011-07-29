<?php

namespace mageekguy\atoum\tests\units\asserters\adapter\call;

require_once(__DIR__ . '/../../../../runner.php');

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
		$call = new call\adapter(
				$adapterAsserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score()))),
				$adapter = new test\adapter(),
				$functionName = uniqid()
		);

		$this->assert
			->object($call->getMockAsserter())->isIdenticalTo($adapterAsserter)
			->object($call->getAdapter())->isIdenticalTo($adapter)
			->string($call->getFunctionName())->isEqualTo($functionName)
			->variable($call->getArguments())->isNull()
		;

		$call = new call\adapter(
				$adapterAsserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score()))),
				$adapter = new test\adapter,
				$functionName = rand(1, PHP_INT_MAX)
		);

		$this->assert
			->object($call->getMockAsserter())->isIdenticalTo($adapterAsserter)
			->object($call->getAdapter())->isIdenticalTo($adapter)
			->string($call->getFunctionName())->isEqualTo((string) $functionName)
			->variable($call->getArguments())->isNull()
		;
	}

	/*
	public function test__call()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\asserters\mock')
		;

		$call = new call\adapter(
				$adapterAsserter = new \mock\mageekguy\atoum\asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))),
				new test\adapter(),
				uniqid()
		);

		$adapterAsserter->getMockController()->call = $adapterAsserter;

		$this->assert
			->object($call->call($arg = uniqid()))->isIdenticalTo($adapterAsserter)
			->mock($adapterAsserter)
				->call('call')->withArguments($arg)->once()
		;

		$unknownFunction = uniqid();

		$this->assert
			->exception(function() use ($call, $unknownFunction) {
						$call->{$unknownFunction}();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Method ' . get_class($adapterAsserter) . '::' . $unknownFunction . '() does not exist')
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
	*/
}

?>
