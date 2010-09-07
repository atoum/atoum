<?php

namespace mageekguy\atoum\tests\units\reporters\cli;

use \mageekguy\atoum;
use \mageekguy\atoum\reporters\cli;

require_once(__DIR__ . '/../../../../runners/autorunner.php');

class progressBar extends atoum\test
{
	public function test__construct()
	{
		$mockGenerator = new atoum\mock\generator();

		$mockGenerator->generate(__CLASS__);

		$test = new atoum\mock\mageekguy\atoum\tests\units\reporters\cli\progressBar();
		$mockController = $test->getMockController();

		$mockController->count = function() { return 0; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('_', 60) . '][0/0]')
		;

		$mockController->count = function() { return 1; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('.', sizeof($test)). str_repeat('_', 60 - sizeof($test)) . '][0/' . sizeof($test) . ']')
		;

		$count = rand(2, 9);

		$mockController->count = function() use ($count) { return $count; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('.', sizeof($test)). str_repeat('_', 60 - sizeof($test)) . '][0/' . sizeof($test) . ']')
		;

		$count = rand(10, 60);

		$mockController->count = function() use ($count) { return $count; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('.', sizeof($test)). str_repeat('_', 60 - sizeof($test)) . '][ 0/' . sizeof($test) . ']')
		;

		$mockController->count = function() { return 61; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('.', 59) . '>][ 0/' . sizeof($test) . ']')
		;

		$count = rand(100, PHP_INT_MAX);
		$mockController->count = function() use ($count) { return $count; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('.', 59) . '>][' . sprintf('%' . strlen((string) $count) . 'd', 0) . '/' . sizeof($test) . ']')
		;
	}

	public function testUpdate()
	{
		$mockGenerator = new atoum\mock\generator();

		$mockGenerator->generate(__CLASS__);

		$test = new atoum\mock\mageekguy\atoum\tests\units\reporters\cli\progressBar();
		$mockController = $test->getMockController();

		$mockController->count = function() { return 0; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->object($progressBar->update('F'))->isIdenticalTo($progressBar)
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('_', 60) . '][0/0]')
		;

		$mockController->count = function() { return 1; };

		$progressBar = new cli\progressBar($test);

		$length = strlen((string) $progressBar);

		$this->assert
			->object($progressBar->update('F'))->isIdenticalTo($progressBar)
			->string(addcslashes((string) $progressBar, "\010"))->isEqualTo(addcslashes(str_repeat("\010", $length - 1) . 'F' . str_repeat('_', 59) . '][1/1]', "\010"))
		;
	}
}

?>
