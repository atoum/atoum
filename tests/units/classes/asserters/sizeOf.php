<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\tools\diffs
;

require_once __DIR__ . '/../../runner.php';

class sizeOf extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\integer');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\sizeOf($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\sizeOf($generator = new asserter\generator()))
			->then
				->object($asserter->setWith(array()))->isIdenticalTo($asserter)
				->boolean($asserter->wasSet())->isTrue()
				->integer($asserter->getValue())->isZero()
				->object($asserter->setWith($countable = range(1, rand(2, 5))))->isIdenticalTo($asserter)
				->boolean($asserter->wasSet())->isTrue()
				->integer($asserter->getValue())->isEqualTo(sizeof($countable))
		;
	}
}
