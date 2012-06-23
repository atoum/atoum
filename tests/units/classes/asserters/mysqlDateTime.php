<?php

namespace atoum\tests\units\asserters;

use
	atoum,
	atoum\asserter,
	atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class mysqlDateTime extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('atoum\asserters\dateTime');
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\mysqlDateTime($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not in format Y-m-d H:i:s'), $asserter->getTypeOf($value)))
				->string($asserter->getValue())->isEqualTo($value)
			->object($asserter->setWith($value = '1976-10-06 14:05:54'))->isIdenticalTo($asserter)
			->string($asserter->getValue())->isIdenticalTo($value)
			->object($asserter->setWith($value = uniqid(), false))->isIdenticalTo($asserter)
			->string($asserter->getValue())->isEqualTo($value)
		;
	}
}
