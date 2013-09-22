<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mock\mageekguy\atoum\asserters\call as testedClass
;

require_once __DIR__ . '/../../runner.php';

class call extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->isSubclassOf('mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->variable($asserter->getCall())->isNull()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->variable($asserter->getCall())->isNull()
				->object($asserter->setWith($call = new php\call(uniqid())))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isCloneOf($call)
		;
	}

	public function testWithArguments()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->setWith($call = new php\call($function = uniqid())))
			->then
				->object($asserter->withArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, array()))
				->object($asserter->withArguments($arg1 = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg1)))
				->object($asserter->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg1, $arg2)))
		;
	}

	public function testWithAnyArguments()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->exception(function() use ($asserter) { $asserter->withArguments(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->setWith($call = new php\call($function = uniqid())))
			->then
				->object($asserter->getCall())->isEqualTo(new php\call($function))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function))
			->if($asserter->withArguments($arg = uniqid()))
			->then
				->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg)))
				->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function))
		;
	}

	public function testWithoutAnyArgument()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->exception(function() use ($asserter) { $asserter->withoutAnyArgument(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
			->if($asserter->setWith($call = new php\call($function = uniqid())))
			->then
				->object($asserter->withoutAnyArgument())->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, array()))
		;
	}
}
