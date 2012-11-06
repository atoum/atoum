<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\failures\execute\unix;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\failures\execute\unix\gedit as testedClass
;

require_once __DIR__ . '/../../../../../../../runner.php';

class gedit extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->isSubclassOf('mageekguy\atoum\report\fields\runner\failures\execute')
		;
	}

	public function testGetCommand()
	{
		$this
			->if($field = new testedClass())
			->then
				->string($field->getCommand())->isEqualTo('gedit %1$s +%2$d > /dev/null &')
		;
	}
}
