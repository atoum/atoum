<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable,
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
			->given($asserter = $this->newTestedInstance)
			->then
				->object($asserter->getGenerator())->isEqualTo(new asserter\generator())
				->object($asserter->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($asserter->getLocale())->isIdenticalTo($asserter->getGenerator()->getLocale())
				->string($asserter->getValue())->isEmpty()
				->boolean($asserter->wasSet())->isTrue()

			->if($asserter = $this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer()))
			->then
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getAnalyzer())->isIdenticalTo($analyzer)
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->string($asserter->getValue())->isEmpty()
				->boolean($asserter->wasSet())->isTrue()
		;
	}

	public function testSetWith()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->object($asserter->setWith(function() use (& $output) { echo ($output = uniqid()); }))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($output)
				->variable($asserter->getCharlist())->isNull()
				->object($asserter->setWith(function() use (& $output) { echo ($output = uniqid()); }, "\010"))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($output)
				->string($asserter->getCharlist())->isEqualTo("\010")
		;
	}
}
