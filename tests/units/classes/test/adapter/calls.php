<?php

namespace mageekguy\atoum\tests\units\test\adapter;

require __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\test\adapter\calls as testedClass
;

class calls extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->implements('countable')
			->implements('arrayAccess')
		;
	}

	public function test__construct()
	{
		$this
			->if($calls = new testedClass())
			->then
				->sizeof($calls)->isZero()
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($calls = new testedClass())
			->and($calls[] = $call = new adapter\call(uniqid()))
			->then
				->array($calls[$call->getFunction()])
					->isEqualTo(array(1 => $call))
						->object[1]->isCloneOf($call)
			->if($calls[] = $otherCall = new adapter\call($call->getFunction()))
			->then
				->array($calls[$call->getFunction()])
					->isEqualTo(array(1 => $call, 2 => $otherCall))
						->object[1]->isCloneOf($call)
						->object[2]->isCloneOf($otherCall)
		;
	}
}
