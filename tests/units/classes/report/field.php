<?php

namespace mageekguy\atoum\tests\units\report;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;

require_once(__DIR__ . '/../../runner.php');

class field extends atoum\test
{
	public function test__construct()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\report\field');

		$field = new mock\mageekguy\atoum\report\field();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getEvent())->isNull()
		;

		$field = new mock\mageekguy\atoum\report\field($locale = new atoum\locale());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getEvent())->isNull()
		;
	}

	public function testSetEvent()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\report\field');

		$field = new mock\mageekguy\atoum\report\field();

		$this->assert
			->object($field->setEvent($event = uniqid()))->isIdenticalTo($field)
			->string($field->getEvent())->isEqualTo($event)
		;
	}
}

?>
