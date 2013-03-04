<?php

namespace mageekguy\atoum\tests\units;

require __DIR__ . '/../runner.php';

use
	mageekguy\atoum,
	mock\mageekguy\atoum\asserter as testedClass
;

class asserter extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($asserter = new testedClass($generator = new atoum\asserter\generator()))
			->then
				->object($asserter->getGenerator())->isIdenticalTo($generator)
			->if($asserter = new testedClass())
			->then
				->object($asserter->getGenerator())->isEqualTo(new atoum\asserter\generator())
		;
	}

	public function testSetGenerator()
	{
		$this
			->if($asserter = new testedClass(new atoum\asserter\generator()))
			->then
				->object($asserter->setGenerator($generator = new atoum\asserter\generator()))->isIdenticalTo($asserter)
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->setGenerator())->isIdenticalTo($asserter)
				->object($asserter->getGenerator())
					->isNotIdenticalTo($generator)
					->isEqualTo(new atoum\asserter\generator())
		;
	}

	public function testSetWithTest()
	{
		$this
			->if($asserter = new testedClass(new atoum\asserter\generator()))
			->then
				->object($asserter->setWithTest($this))->isIdenticalTo($asserter)
		;
	}

	public function testSetWithArguments()
	{
		$this
			->if($asserter = new testedClass(new atoum\asserter\generator()))
			->then
				->object($asserter->setWithArguments(array()))->isIdenticalTo($asserter)
				->mock($asserter)->call('setWith')->never()
				->object($asserter->setWithArguments(array($argument = uniqid())))->isIdenticalTo($asserter)
				->mock($asserter)->call('setWith')->withArguments($argument)->once()
		;
	}
}
