<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class output extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\string');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\output($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->string($asserter->getValue())->isEmpty()
				->boolean($asserter->wasSet())->isTrue()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\output(new asserter\generator()))
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
