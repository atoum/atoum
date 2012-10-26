<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests;

use
	mageekguy\atoum,
	mageekguy\atoum\runner,
	mock\mageekguy\atoum\report\fields\runner\tests\skipped as testedClass
;

require __DIR__ . '/../../../../../runner.php';

class skipped extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->extends('mageekguy\atoum\report\field')
			->isAbstract()
		;
	}

	public function test__construct()
	{
		$this
			->if($field = new testedClass())
			->then
				->variable($field->getRunner())->isNull()
				->array($field->getEvents())->isEqualTo(array(runner::runStop))
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new testedClass())
			->and($runner = new atoum\runner())
			->then
				->boolean($field->handleEvent(runner::runStart, $runner))->isFalse()
				->variable($field->getRunner())->isNull()
				->boolean($field->handleEvent(runner::runStop, $runner))->isTrue()
				->object($field->getRunner())->isIdenticalTo($runner)
		;
	}
}
