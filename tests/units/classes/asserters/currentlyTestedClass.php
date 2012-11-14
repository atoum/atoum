<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class currentlyTestedClass extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\phpClass');
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\currentlyTestedClass(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->setWith(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\badMethodCall')
					->hasMessage('Unable to call method ' . get_class($asserter) . '::setWith()')
		;
	}
}
