<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters\output as sut
;

require_once __DIR__ . '/../../runner.php';

class output extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\string');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new sut())
			->then
				->object($asserter->getGenerator())->isEqualTo(new asserter\generator())
				->object($asserter->getAdapter())->isEqualTo(new atoum\adapter())
				->object($asserter->getLocale())->isIdenticalTo($asserter->getGenerator()->getLocale())
				->string($asserter->getValue())->isEmpty()
				->boolean($asserter->wasSet())->isTrue()
			->if($asserter = new sut($generator = new asserter\generator(), $adapter = new atoum\adapter()))
			->then
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->string($asserter->getValue())->isEmpty()
				->boolean($asserter->wasSet())->isTrue()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut(new asserter\generator()))
			->then
				->object($asserter->setWith(function() use (& $output) { echo ($output = uniqid()); }))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($output)
				->variable($asserter->getCharlist())->isNull()
				->object($asserter->setWith(function() use (& $output) { echo ($output = uniqid()); }, null, "\010"))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($output)
				->string($asserter->getCharlist())->isEqualTo("\010")
		;
	}
}
