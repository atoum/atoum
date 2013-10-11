<?php

namespace mageekguy\atoum\tests\units\asserters;

require __DIR__ . '/../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\asserters\phpFunction as sut
;

class phpFunction extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\adapter\call');
	}

	public function testSetWithTest()
	{
		$this
			->if($asserter = new sut())
			->then
				->object($asserter->setWithTest($this))->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isCloneOf(php\mocker::getAdapter())
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut())
			->then
				->object($asserter->setWith($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isCloneOf(php\mocker::getAdapter())
				->string($asserter->getCall()->getFunction())->isEqualTo($function)
		;
	}

	public function testHandleNativeType()
	{
		$this
			->if($asserter = new sut(new atoum\asserter\generator()))
			->then
				->boolean($asserter->handleNativeType())->isFalse()
		;
	}
}
