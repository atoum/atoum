<?php

namespace mageekguy\atoum\tests\units\report;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;

require_once(__DIR__ . '/../../runner.php');

class decorator extends atoum\test
{
	public function test__construct()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\report\decorator');

		$decorator = new mock\mageekguy\atoum\report\decorator();

		$this->assert
			->array($decorator->getWriters())->isEmpty()
		;
	}

	public function testAddWriter()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\writer')
			->generate('\mageekguy\atoum\report\decorator')
		;

		$decorator = new mock\mageekguy\atoum\report\decorator();

		$this->assert
			->object($decorator->addWriter($writer = new mock\mageekguy\atoum\writer()))->isIdenticalTo($decorator)
			->array($decorator->getWriters())->isEqualTo(array($writer))
			->object($decorator->addWriter($otherWriter = new mock\mageekguy\atoum\writer()))->isIdenticalTo($decorator)
			->array($decorator->getWriters())->isEqualTo(array($writer, $otherWriter))
		;
	}
}

?>
