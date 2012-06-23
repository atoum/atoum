<?php

namespace mageekguy\atoum\tests\units\report;

use
	mageekguy\atoum,
	mageekguy\atoum\report
;

require_once __DIR__ . '/../../runner.php';

class field extends atoum\test
{
	public function test__construct()
	{
		$this
			->assert
			->if($field = new \mock\mageekguy\atoum\report\field())
			->then
				->variable($field->getEvents())->isNull()
				->object($field->getLocale())->isInstanceOf('mageekguy\atoum\locale')
			->if($field = new \mock\mageekguy\atoum\report\field($events = array(uniqid(), uniqid(), uniqid()), $locale = new atoum\locale()))
			->then
				->array($field->getEvents())->isEqualTo($events)
				->object($field->getLocale())->isIdenticalTo($locale)
		;
	}
}
