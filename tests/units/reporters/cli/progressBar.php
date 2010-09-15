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
			->string((string) $progressBar)->isEmpty()
		;

		$mockController->count = function() { return 1; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('.', sizeof($test)). str_repeat('_', 60 - sizeof($test)) . '][0/' . sizeof($test) . ']')
			->string((string) $progressBar)->isEmpty()
		;

		$count = rand(2, 9);

		$mockController->count = function() use ($count) { return $count; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('.', sizeof($test)). str_repeat('_', 60 - sizeof($test)) . '][0/' . sizeof($test) . ']')
			->string((string) $progressBar)->isEmpty()
		;

		$count = rand(10, 60);

		$mockController->count = function() use ($count) { return $count; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('.', sizeof($test)). str_repeat('_', 60 - sizeof($test)) . '][ 0/' . sizeof($test) . ']')
			->string((string) $progressBar)->isEmpty()
		;

		$mockController->count = function() { return 61; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('.', 59) . '>][ 0/' . sizeof($test) . ']')
			->string((string) $progressBar)->isEmpty()
		;

		$count = rand(100, PHP_INT_MAX);
		$mockController->count = function() use ($count) { return $count; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('.', 59) . '>][' . sprintf('%' . strlen((string) $count) . 'd', 0) . '/' . sizeof($test) . ']')
			->string((string) $progressBar)->isEmpty()
		;
	}

	public function testRefresh()
	{
		$mockGenerator = new atoum\mock\generator();

		$mockGenerator->generate(__CLASS__);

		$test = new atoum\mock\mageekguy\atoum\tests\units\reporters\cli\progressBar();
		$mockController = $test->getMockController();

		$mockController->count = function() { return 0; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->string((string) $progressBar)->isEqualTo('[' . str_repeat('_', 60) . '][0/0]')
			->string((string) $progressBar)->isEmpty()
		;

		$mockController->count = function() { return 1; };

		$progressBar = new cli\progressBar($test);

		$progressBarString = (string) $progressBar;
		$progressBarLength = strlen($progressBarString);

		$this->assert
			->string($progressBarString)->isEqualTo('[' . str_repeat('.', sizeof($test)). str_repeat('_', 60 - sizeof($test)) . '][0/' . sizeof($test) . ']')
			->string((string) $progressBar)->isEmpty()
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->string((string) $progressBar, "\010")->isEqualTo(str_repeat("\010", $progressBarLength - 1) . 'F' . str_repeat('_', 59) . '][1/1]')
			->string((string) $progressBar)->isEmpty()
		;

		$progressBar = new cli\progressBar($test);

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->string((string) $progressBar, "\010")->isEqualTo('[' . str_repeat('.', sizeof($test)). str_repeat('_', 60 - sizeof($test)) . '][0/' . sizeof($test) . ']' . str_repeat("\010", $progressBarLength - 1) . 'F' . str_repeat('_', 59) . '][1/1]')
			->string((string) $progressBar)->isEmpty()
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->string((string) $progressBar)->isEmpty()
		;

		$mockController->count = function() { return 60; };

		$progressBar = new cli\progressBar($test);

		$progressBarString = (string) $progressBar;

		$this->assert
			->string($progressBarString)->isEqualTo('[' . str_repeat('.', 60) . '][ 0/60]')
			->string((string) $progressBar)->isEmpty()
		;

		$nextProgressBarString = 'F' . str_repeat('.', 59) . '][ 1/60]';

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->string((string) $progressBar, "\010")->isEqualTo(str_repeat("\010", strlen($progressBarString) - 1) . $nextProgressBarString)
			->string((string) $progressBar)->isEmpty()
		;

		for ($i = 2; $i <= 60; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 60 - $i) . '][' . sprintf('%2d', $i) . '/60]';

			$this->assert
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->string((string) $progressBar, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->string((string) $progressBar)->isEmpty()
			;
		}

		$this->assert
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->string((string) $progressBar)->isEmpty()
		;

		$mockController->count = function() { return 61; };

		$progressBar = new cli\progressBar($test);

		$progressBarString = (string) $progressBar;

		$this->assert
			->string($progressBarString)->isEqualTo('[' . str_repeat('.', 59) . '>][ 0/61]')
			->string((string) $progressBar)->isEmpty()
		;

		$nextProgressBarString = 'F' . str_repeat('.', 58) . '>][ 1/61]';

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->string((string) $progressBar, "\010")->isEqualTo(str_repeat("\010", strlen($progressBarString) - 1) . $nextProgressBarString)
			->string((string) $progressBar)->isEmpty()
		;

		for ($i = 2; $i <= 58; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 59 - $i) . '>][' . sprintf('%2d', $i) . '/61]';

			$this->assert
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->string((string) $progressBar, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->string((string) $progressBar)->isEmpty()
			;
		}

		$currentProgressBarString = $nextProgressBarString;

		$nextProgressBarString = '[..' . str_repeat('_', 58) . ']';

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->string((string) $progressBar, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . 'F>][59/61]' . "\n" . $nextProgressBarString)
			->string((string) $progressBar)->isEmpty()
		;

		$currentProgressBarString = $nextProgressBarString;

		$nextProgressBarString = 'F.' . str_repeat('_', 58) . '][60/61]';

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->string((string) $progressBar, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
			->string((string) $progressBar)->isEmpty()
		;

		$currentProgressBarString = $nextProgressBarString;

		$nextProgressBarString = 'F' . str_repeat('_', 58) . '][61/61]';

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->string((string) $progressBar, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
			->string((string) $progressBar)->isEmpty()
		;
	}
}

?>
