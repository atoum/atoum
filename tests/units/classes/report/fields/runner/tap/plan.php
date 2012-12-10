<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tap;

use
	mageekguy\atoum,
	mageekguy\atoum\runner,
	mageekguy\atoum\report\fields\runner\tap\plan as testedClass
;

require __DIR__ . '/../../../../../runner.php';

class plan extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\field');
	}

	public function test__construct()
	{
		$this
			->if($field = new testedClass())
			->then
				->array($field->getEvents())->isEqualTo(array(runner::runStart))
		;
	}

	public function test__toString()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\runner())
			->and($this->calling($runner)->getTestMethodNumber = $testMethodNumber = rand(1, PHP_INT_MAX))
			->and($field = new testedClass())
			->if($field->handleEvent(runner::runStop, $runner))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(runner::runStart, $runner))
			->then
				->castToString($field)->isEqualTo('1..' . $testMethodNumber . PHP_EOL)
		;
	}
}
