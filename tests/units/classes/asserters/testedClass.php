<?php

namespace atoum\tests\units\asserters;

use
	atoum,
	atoum\asserter,
	atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class testedClass extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('atoum\asserters\phpClass');
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\testedClass(new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->setWith(uniqid()); })
					->isInstanceOf('atoum\exceptions\logic\badMethodCall')
					->hasMessage('Unable to call method ' . get_class($asserter) . '::setWith()')
		;
	}
}
