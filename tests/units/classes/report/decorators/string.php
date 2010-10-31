<?php

namespace mageekguy\atoum\tests\units\report\decorators;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\decorators;

require_once(__DIR__ . '/../../../runner.php');

class string extends atoum\test
{
	public function testWrite()
	{
		$decorator = new decorators\string();

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\writer')
			->generate('\mageekguy\atoum\report\field')
		;

		$writer = new mock\mageekguy\atoum\writer();
		$writer->getMockController()->write = function($something) {};

		$decorator->addWriter($writer);

		$toString = uniqid();

		$field = new mock\mageekguy\atoum\report\field();
		$field->getMockController()->toString = function() use ($toString) { return $toString; };

		$this->assert
			->object($decorator->write($field))->isIdenticalTo($decorator)
			->mock($writer)
				->call('write', array($toString))
		;
	}
}

?>
