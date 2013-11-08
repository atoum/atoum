<?php

namespace mageekguy\atoum\tests\units\asserters\adapter;

require __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter,
	mock\mageekguy\atoum\asserters\adapter\call as testedClass
;

class call extends atoum
{
	public function testClass()
	{
		$this->testedClass->isAbstract()->extends('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->variable($asserter->getLastAssertionFile())->isNull()
				->variable($asserter->getLastAssertionLine())->isNull()
		;
	}

	public function testBefore()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->object($asserter->before($asserter1 = new testedClass()))->isIdenticalTo($asserter)
				->array($asserter->getBefore())->isEqualTo(array($asserter1))
				->variable($asserter->getLastAssertionFile())->isNotNull()
				->variable($asserter->getLastAssertionLine())->isNotNull()
				->object($asserter->before(
							$asserter2 = new testedClass(),
							$asserter3 = new testedClass()
						)
					)->isIdenticalTo($asserter)
				->array($asserter->getBefore())->isEqualTo(array($asserter1, $asserter2, $asserter3))
				->variable($asserter->getLastAssertionFile())->isNotNull()
				->variable($asserter->getLastAssertionLine())->isNotNull()
		;
	}

	public function testAfter()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->object($asserter->after($asserter1 = new testedClass()))->isIdenticalTo($asserter)
				->array($asserter->getAfter())->isEqualTo(array($asserter1))
				->variable($asserter->getLastAssertionFile())->isNotNull()
				->variable($asserter->getLastAssertionLine())->isNotNull()
				->object($asserter->after(
							$asserter2 = new testedClass(),
							$asserter3 = new testedClass()
						)
					)->isIdenticalTo($asserter)
				->array($asserter->getAfter())->isEqualTo(array($asserter1, $asserter2, $asserter3))
				->variable($asserter->getLastAssertionFile())->isNotNull()
				->variable($asserter->getLastAssertionLine())->isNotNull()
		;
	}
}
