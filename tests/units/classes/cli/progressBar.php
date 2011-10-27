<?php

namespace mageekguy\atoum\tests\units\cli;

use
	mageekguy\atoum,
	mageekguy\atoum\cli,
	mageekguy\atoum\mock
;

require_once __DIR__ . '/../../runner.php';

class progressBar extends atoum\test
{
	public function beforeTestMethod($testMethod)
	{
		if (defined('STDOUT') === false)
		{
			define('STDOUT', uniqid());
		}
	}

	public function test__construct()
	{
		$this->mock('mageekguy\atoum\test');

		$testAdapter = new atoum\test\adapter();
		$testAdapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new \mock\mageekguy\atoum\test(null, null, $testAdapter, null, $testController);

		$testController->count = function() { return 0; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->object($progressBar->getCli())->isEqualTo(new atoum\cli())
			->castToString($progressBar)->isEqualTo('[' . str_repeat('_', 60) . '][0/0]')
			->castToString($progressBar)->isEmpty()
		;

		$testController->count = function() { return 1; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->castToString($progressBar)->isEqualTo('[' . str_repeat('.', sizeof($test)) . str_repeat('_', 60 - sizeof($test)) . '][0/' . sizeof($test) . ']')
			->castToString($progressBar)->isEmpty()
		;

		$count = rand(2, 9);
		$testController->count = function() use ($count) { return $count; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->castToString($progressBar)->isEqualTo('[' . str_repeat('.', sizeof($test)) . str_repeat('_', 60 - sizeof($test)) . '][0/' . sizeof($test) . ']')
			->castToString($progressBar)->isEmpty()
		;

		$count = rand(10, 60);
		$testController->count = function() use ($count) { return $count; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->castToString($progressBar)->isEqualTo('[' . str_repeat('.', sizeof($test)) . str_repeat('_', 60 - sizeof($test)) . '][ 0/' . sizeof($test) . ']')
			->castToString($progressBar)->isEmpty()
		;

		$testController->count = function() { return 61; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->castToString($progressBar)->isEqualTo('[' . str_repeat('.', 59) . '>][ 0/' . sizeof($test) . ']')
			->castToString($progressBar)->isEmpty()
		;

		$count = rand(100, PHP_INT_MAX);
		$testController->count = function() use ($count) { return $count; };

		$progressBar = new cli\progressBar($test);

		$this->assert
			->castToString($progressBar)->isEqualTo('[' . str_repeat('.', 59) . '>][' . sprintf('%' . strlen((string) sizeof($test)) . 'd', 0) . '/' . sizeof($test) . ']')
			->castToString($progressBar)->isEmpty()
		;
	}

	public function testRefresh()
	{
		$this
			->mock('mageekguy\atoum\cli')
			->mock('mageekguy\atoum\test')
		;

		$testAdapter = new atoum\test\adapter();
		$testAdapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new \mock\mageekguy\atoum\test(null, null, $testAdapter, null, $testController);

		$testController->count = function() { return 0; };

		$cli = new \mock\mageekguy\atoum\cli();
		$cli->getMockController()->isTerminal = true;

		$progressBar = new cli\progressBar($test, $cli);

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar)->isEqualTo('[' . str_repeat('_', 60) . '][0/0]')
			->castToString($progressBar)->isEmpty()
		;

		$testController->count = function() { return 1; };

		$progressBar = new cli\progressBar($test, $cli);

		$progressBarString = (string) $progressBar;
		$progressBarLength = strlen($progressBarString);

		$this->assert
			->string($progressBarString)->isEqualTo('[' . str_repeat('.', sizeof($test)) . str_repeat('_', 60 - sizeof($test)) . '][0/' . sizeof($test) . ']')
			->castToString($progressBar)->isEmpty()
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", $progressBarLength - 1) . 'F' . str_repeat('_', 59) . '][1/1]')
			->castToString($progressBar)->isEmpty()
		;

		$progressBar = new cli\progressBar($test, $cli);

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar, null, "\010")->isEqualTo('[' . str_repeat('.', sizeof($test)) . str_repeat('_', 60 - sizeof($test)) . '][0/' . sizeof($test) . ']' . str_repeat("\010", $progressBarLength - 1) . 'F' . str_repeat('_', 59) . '][1/1]')
			->castToString($progressBar)->isEmpty()
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar)->isEmpty()
		;

		$testController->count = function() { return 60; };

		$progressBar = new cli\progressBar($test, $cli);

		$progressBarString = (string) $progressBar;

		$this->assert
			->string($progressBarString)->isEqualTo('[' . str_repeat('.', 60) . '][ 0/60]')
			->castToString($progressBar)->isEmpty()
		;

		$nextProgressBarString = 'F' . str_repeat('.', 59) . '][ 1/60]';

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($progressBarString) - 1) . $nextProgressBarString)
			->castToString($progressBar)->isEmpty()
		;

		for ($i = 2; $i <= 60; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 60 - $i) . '][' . sprintf('%2d', $i) . '/60]';

			$this->assert
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			;
		}

		$this->assert
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar)->isEmpty()
		;

		$testController->count = function() { return 61; };

		$progressBar = new cli\progressBar($test, $cli);

		$progressBarString = (string) $progressBar;

		$this->assert
			->string($progressBarString)->isEqualTo('[' . str_repeat('.', 59) . '>][ 0/61]')
			->castToString($progressBar)->isEmpty()
		;

		$nextProgressBarString = 'F' . str_repeat('.', 58) . '>][ 1/61]';

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($progressBarString) - 1) . $nextProgressBarString)
			->castToString($progressBar)->isEmpty()
		;

		for ($i = 2; $i <= 58; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 59 - $i) . '>][' . sprintf('%2d', $i) . '/61]';

			$this->assert
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			;
		}

		$currentProgressBarString = $nextProgressBarString;

		$nextProgressBarString = '[..' . str_repeat('_', 58) . ']';

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . 'F>][59/61]' . "\n" . $nextProgressBarString)
			->castToString($progressBar)->isEmpty()
		;

		$currentProgressBarString = $nextProgressBarString;

		$nextProgressBarString = 'F.' . str_repeat('_', 58) . '][60/61]';

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
			->castToString($progressBar)->isEmpty()
		;

		$currentProgressBarString = $nextProgressBarString;

		$nextProgressBarString = 'F' . str_repeat('_', 58) . '][61/61]';

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
			->castToString($progressBar)->isEmpty()
		;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new \mock\mageekguy\atoum\test(null, null, $testAdapter, null, $testController);

		$testController->count = function() { return 0; };

		$cli->getMockController()->isTerminal = false;

		$progressBar = new cli\progressBar($test, $cli);

		$this->assert
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar)->isEqualTo('[' . str_repeat('_', 60) . '][0/0]')
			->castToString($progressBar)->isEmpty()
		;

		$testController->count = 3;

		$progressBar = new cli\progressBar($test, $cli);

		$progressBarString = (string) $progressBar;
		$progressBarLength = strlen($progressBarString);

		$this->assert
			->string($progressBarString)->isEqualTo('[' . str_repeat('.', sizeof($test)) . str_repeat('_', 60 - sizeof($test)) . '][0/' . sizeof($test) . ']')
			->castToString($progressBar)->isEmpty()
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar)->isEqualTo(PHP_EOL . '[' . 'F..' . str_repeat('_', 57) . '][1/3]')
			->castToString($progressBar)->isEmpty()
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar)->isEqualTo(PHP_EOL . '[' . 'FF.' . str_repeat('_', 57) . '][2/3]')
			->castToString($progressBar)->isEmpty()
			->object($progressBar->refresh('S'))->isIdenticalTo($progressBar)
			->castToString($progressBar)->isEqualTo(PHP_EOL . '[' . 'FFS' . str_repeat('_', 57) . '][3/3]')
			->castToString($progressBar)->isEmpty()
		;
	}
}

?>
