<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\failures;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\failures\phpstorm as testedClass
;

require_once __DIR__ . '/../../../../../runner.php';

class phpstorm extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->isSubclassOf('mageekguy\atoum\report\fields\runner\failures\execute')
		;
	}

	public function test__construct()
	{
		$this
			->if($field = new testedClass($command = uniqid()))
			->then
				->string($field->getCommand())->contains($command)
		;
	}

	public function testGetCommand()
	{
		$this
			->if($field = new testedClass($command = uniqid()))
			->then
				->string($field->getCommand())->isEqualTo($command . ' --line %2$s %1$s &')
		;
	}
}
