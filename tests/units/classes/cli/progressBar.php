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
	public function testClassConstants()
	{
		$this
			->string(cli\progressBar::defaultProgressBarFormat)->isEqualTo('[%s]')
			->string(cli\progressBar::defaultCounterFormat)->isEqualTo('[%s/%s]')
		;
	}

	public function test__construct()
	{
		$this
			->if($progressBar = new cli\progressBar(0))
			->then
				->object($progressBar->getCli())->isEqualTo(new atoum\cli())
				->castToString($progressBar)->isEqualTo('[' . str_repeat('_', 60) . '][0/0]')
				->castToString($progressBar)->isEmpty()
			->if($progressBar = new cli\progressBar(1))
			->then
				->castToString($progressBar)->isEqualTo('[.' . str_repeat('_', 59) . '][0/1]')
				->castToString($progressBar)->isEmpty()
			->if($progressBar = new cli\progressBar($count = rand(2, 9)))
			->then
				->castToString($progressBar)->isEqualTo('[' . str_repeat('.', $count) . str_repeat('_', 60 - $count) . '][0/' . $count . ']')
				->castToString($progressBar)->isEmpty()
			->if($progressBar = new cli\progressBar($count = rand(10, 60)))
			->then
				->castToString($progressBar)->isEqualTo('[' . str_repeat('.', $count) . str_repeat('_', 60 - $count) . '][ 0/' . $count . ']')
				->castToString($progressBar)->isEmpty()
			->if($progressBar = new cli\progressBar(61))
			->then
				->castToString($progressBar)->isEqualTo('[' . str_repeat('.', 59) . '>][ 0/61]')
				->castToString($progressBar)->isEmpty()
			->if($progressBar = new cli\progressBar($count = rand(100, PHP_INT_MAX)))
			->then
				->castToString($progressBar)->isEqualTo('[' . str_repeat('.', 59) . '>][' . sprintf('%' . strlen((string) $count) . 'd', 0) . '/' . $count . ']')
				->castToString($progressBar)->isEmpty()
		;
	}

	public function testRefresh()
	{
		$this
			->if($cli = new \mock\mageekguy\atoum\cli())
			->and($cli->getMockController()->isTerminal = true)
			->and($progressBar = new cli\progressBar(0, $cli))
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar)->isEqualTo('[' . str_repeat('_', 60) . '][0/0]')
				->castToString($progressBar)->isEmpty()
			->if($progressBar = new cli\progressBar(1, $cli))
			->and($progressBarString = (string) $progressBar)
			->and($progressBarLength = strlen($progressBarString))
			->then
				->string($progressBarString)->isEqualTo('[.' . str_repeat('_', 59) . '][0/1]')
				->castToString($progressBar)->isEmpty()
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", $progressBarLength - 1) . 'F' . str_repeat('_', 59) . '][1/1]')
				->castToString($progressBar)->isEmpty()
			->if($progressBar = new cli\progressBar(1, $cli))
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo('[.' . str_repeat('_', 59) . '][0/1]' . str_repeat("\010", $progressBarLength - 1) . 'F' . str_repeat('_', 59) . '][1/1]')
				->castToString($progressBar)->isEmpty()
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar)->isEmpty()
			->if($progressBar = new cli\progressBar(60, $cli))
			->and($progressBarString = (string) $progressBar)
			->then
				->string($progressBarString)->isEqualTo('[' . str_repeat('.', 60) . '][ 0/60]')
				->castToString($progressBar)->isEmpty()
			->if($nextProgressBarString = 'F' . str_repeat('.', 59) . '][ 1/60]')
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($progressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
		;

		for ($i = 2; $i <= 60; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 60 - $i) . '][' . sprintf('%2d', $i) . '/60]';

			$this
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			;
		}

		$this
			->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
			->castToString($progressBar)->isEmpty()
		;

		$this
			->if($progressBar = new cli\progressBar(61, $cli))
			->and($progressBarString = (string) $progressBar)
			->then
				->string($progressBarString)->isEqualTo('[' . str_repeat('.', 59) . '>][ 0/61]')
				->castToString($progressBar)->isEmpty()
			->if($nextProgressBarString = 'F' . str_repeat('.', 58) . '>][ 1/61]')
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($progressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
		;

		for ($i = 2; $i <= 58; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 59 - $i) . '>][' . sprintf('%2d', $i) . '/61]';

			$this
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			;
		}

		$this
			->if($currentProgressBarString = $nextProgressBarString)
			->and($nextProgressBarString = '[..' . str_repeat('_', 58) . ']')
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . 'F>][59/61]' . PHP_EOL . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			->if($currentProgressBarString = $nextProgressBarString)
			->and($nextProgressBarString = 'F.' . str_repeat('_', 58) . '][60/61]')
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			->if($currentProgressBarString = $nextProgressBarString)
			->and($nextProgressBarString = 'F' . str_repeat('_', 58) . '][61/61]')
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			->if($progressBar = new cli\progressBar(121, $cli))
			->and($progressBarString = (string) $progressBar)
			->then
				->string($progressBarString)->isEqualTo('[' . str_repeat('.', 59) . '>][  0/121]')
				->castToString($progressBar)->isEmpty()
			->if($nextProgressBarString = 'F' . str_repeat('.', 58) . '>][  1/121]')
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($progressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
		;

		for ($i = 2; $i <= 58; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 59 - $i) . '>][' . sprintf('%3d', $i) . '/121]';

			$this
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			;
		}

		$this
			->if($currentProgressBarString = $nextProgressBarString)
			->and($nextProgressBarString = '[' . str_repeat('.', 59) . '>]')
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . 'F>][ 59/121]' . PHP_EOL . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
		;

		for ($i = 60; $i <= 117; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 118 - $i) . '>][' . sprintf('%3d', $i) . '/121]';

			$this
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			;
		}

		$this
			->if($currentProgressBarString = $nextProgressBarString)
			->and($nextProgressBarString = '[...' . str_repeat('_', 57) . ']')
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . 'F>][118/121]' . PHP_EOL . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
		;

		for ($i = 119; $i <= 121; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 121 - $i) . str_repeat('_', 57) . '][' . sprintf('%3d', $i) . '/121]';

			$this
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			;
		}

		$this
			->if($cli->getMockController()->isTerminal = false)
			->and($progressBar = new cli\progressBar(0, $cli))
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar)->isEqualTo('[' . str_repeat('_', 60) . '][0/0]')
				->castToString($progressBar)->isEmpty()
			->if($progressBar = new cli\progressBar(3, $cli))
			->and($progressBarString = (string) $progressBar)
			->and($progressBarLength = strlen($progressBarString))
			->then
				->string($progressBarString)->isEqualTo('[...' . str_repeat('_', 57) . '][0/3]')
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
			->if($cli->getMockController()->isTerminal = true)
			->and($progressBar = new cli\progressBar(177, $cli))
			->and($progressBarString = (string) $progressBar)
			->then
				->string($progressBarString)->isEqualTo('[' . str_repeat('.', 59) . '>][  0/177]')
				->castToString($progressBar)->isEmpty()
			->if($nextProgressBarString = 'F' . str_repeat('.', 58) . '>][  1/177]')
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($progressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
		;

		for ($i = 2; $i <= 58; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 59 - $i) . '>][' . sprintf('%3d', $i) . '/177]';

			$this
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			;
		}

		$this
			->if($currentProgressBarString = $nextProgressBarString)
			->and($nextProgressBarString = '[' . str_repeat('.', 59) . '>]')
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . 'F>][ 59/177]' . PHP_EOL . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
		;

		for ($i = 60; $i <= 117; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 118 - $i) . '>][' . sprintf('%3d', $i) . '/177]';

			$this
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			;
		}

		$this
			->if($currentProgressBarString = $nextProgressBarString)
			->and($nextProgressBarString = '[' . str_repeat('.', 59) . '_]')
			->then
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . 'F>][118/177]' . PHP_EOL . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
		;

		for ($i = 119; $i <= 176; $i++)
		{
			$currentProgressBarString = $nextProgressBarString;

			$nextProgressBarString = 'F' . str_repeat('.', 177 - $i) . '_][' . sprintf('%3d', $i) . '/177]';

			$this
				->object($progressBar->refresh('F'))->isIdenticalTo($progressBar)
				->castToString($progressBar, null, "\010")->isEqualTo(str_repeat("\010", strlen($currentProgressBarString) - 1) . $nextProgressBarString)
				->castToString($progressBar)->isEmpty()
			;
		}
	}
}
