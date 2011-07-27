<?php

namespace mageekguy\atoum\tests\units\reports;

use
	mageekguy\atoum
;

require_once(__DIR__ . '/../../runner.php');

class asynchronous extends atoum\test
{
	public function testRunnerStop()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\reports\asynchronous')
			->generate('mageekguy\atoum\report\writers\asynchronous')
			->generate('mageekguy\atoum\locale')
		;

		$writer = new \mock\mageekguy\atoum\report\writers\asynchronous();
		$writer->getMockController()->writeAsynchronousReport = function() {};

		$report = new \mock\mageekguy\atoum\reports\asynchronous($locale = new \mock\mageekguy\atoum\locale(), $adapter = new atoum\test\adapter());
		$report->addWriter($writer);

		$runner = new atoum\runner();

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($writer)->call('writeAsynchronousReport', array($report))
			->mock($locale)->call('_')->never()
			->variable($report->getTitle())->isNull()
		;

		$writer->getMockController()->resetCalls();
		$locale->getMockCoNtroller()->resetCalls();

		$report->setTitle($title = uniqid());

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
			->mock($locale)->call('_')->withArguments('SUCCESS')->once()
			->string($report->getTitle())->isEqualTo($title)
		;

		$writer->getMockController()->resetCalls();
		$locale->getMockCoNtroller()->resetCalls();

		$report
			->setTitle($title = '%3$s')
		;

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
			->mock($locale)->call('_')->withArguments('Y-m-d')->once()
			->mock($locale)->call('_')->withArguments('H:i:s')->once()
			->mock($locale)->call('_')->withArguments('SUCCESS')->once()
			->string($report->getTitle())->isEqualTo('SUCCESS')
		;

		$writer->getMockController()->resetCalls();
		$locale->getMockCoNtroller()->resetCalls();

		$adapter->date = function($arg) { return $arg; };

		$report->setTitle($title = '%1$s %2$s %3$s');

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
			->mock($locale)->call('_')->withArguments('Y-m-d')->once()
			->mock($locale)->call('_')->withArguments('H:i:s')->once()
			->mock($locale)->call('_')->withArguments('SUCCESS')->once()
			->string($report->getTitle())->isEqualTo('Y-m-d H:i:s SUCCESS')
		;

		$writer->getMockController()->resetCalls();
		$locale->getMockCoNtroller()->resetCalls();

		$report
			->setTitle($title = '%1$s %2$s %3$s')
			->testAssertionFail(new self())
		;

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
			->mock($locale)->call('_')->withArguments('Y-m-d')->once()
			->mock($locale)->call('_')->withArguments('H:i:s')->once()
			->mock($locale)->call('_')->withArguments('FAIL')->once()
			->string($report->getTitle())->isEqualTo('Y-m-d H:i:s FAIL')
		;
	}
}

?>
