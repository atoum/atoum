<?php

namespace mageekguy\atoum\tests\units\reports;

use \mageekguy\atoum;
use \mageekguy\atoum\mock\mageekguy\atoum as mock;

require_once(__DIR__ . '/../../runner.php');

class asynchronous extends atoum\test
{
	public function testGetFail()
	{
		$this
			->mock('\mageekguy\atoum\reports\asynchronous')
		;
	}

	public function testRunnerStop()
	{
		$this
			->mock('\mageekguy\atoum\reports\asynchronous')
			->mock('\mageekguy\atoum\report\writers\asynchronous')
			->mock('\mageekguy\atoum\locale')
		;

		$writer = new mock\report\writers\asynchronous();
		$writer->getMockController()->writeAsynchronousReport = function() {};

		$report = new mock\reports\asynchronous($locale = new mock\locale(), $adapter = new atoum\test\adapter());
		$report->addWriter($writer);

		$runner = new atoum\runner();

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($writer)->call('writeAsynchronousReport', array($report))
			->mock($locale)->notCall('_')
			->variable($report->getTitle())->isNull()
		;

		$writer->getMockController()->resetCalls();
		$locale->getMockCoNtroller()->resetCalls();

		$report->setTitle($title = uniqid());

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($writer)->call('writeAsynchronousReport', array($report))
			->mock($locale)->call('_', array('SUCCESS'))
			->string($report->getTitle())->isEqualTo($title)
		;

		$writer->getMockController()->resetCalls();
		$locale->getMockCoNtroller()->resetCalls();

		$adapter->date = function($arg) { return $arg; };

		$report->setTitle($title = '%1$s %2$s %3$s');

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($writer)->call('writeAsynchronousReport', array($report))
			->mock($locale)->call('_', array('Y-m-d'))
			->mock($locale)->call('_', array('H:i:s'))
			->mock($locale)->call('_', array('SUCCESS'))
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
			->mock($writer)->call('writeAsynchronousReport', array($report))
			->mock($locale)->call('_', array('Y-m-d'))
			->mock($locale)->call('_', array('H:i:s'))
			->mock($locale)->call('_', array('FAIL'))
			->string($report->getTitle())->isEqualTo('Y-m-d H:i:s FAIL')
		;
	}
}

?>
