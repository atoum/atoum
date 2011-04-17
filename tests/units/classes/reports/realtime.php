<?php

namespace mageekguy\atoum\tests\units\reports;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock\mageekguy\atoum as mock
;

require_once(__DIR__ . '/../../runner.php');

class realtime extends atoum\test
{
	public function testRunnerStart()
	{
		$this
			->mock('\mageekguy\atoum\reports\realtime')
			->mock('\mageekguy\atoum\locale')
		;

		$report = new mock\reports\realtime($locale = new mock\locale(), $adapter = new atoum\test\adapter());

		$this->assert
			->object($report->runnerStart(new atoum\runner()))->isIdenticalTo($report)
			->mock($locale)->wasNotCalled()
		;

		$report->setTitle($title = uniqid());

		$this->assert
			->object($report->runnerStart(new atoum\runner()))->isIdenticalTo($report)
			->mock($locale)->call('_', array('Y-m-d'))
			->mock($locale)->call('_', array('H:i:s'))
			->string($report->getTitle())->isEqualTo($title)
		;

		$adapter->date = function($arg) { return $arg; };

		$report->setTitle('%1$s');

		$locale->getMockController()->resetCalls();

		$this->assert
			->object($report->runnerStart(new atoum\runner()))->isIdenticalTo($report)
			->mock($locale)->call('_', array('Y-m-d'))
			->mock($locale)->call('_', array('H:i:s'))
			->string($report->getTitle())->isEqualTo('Y-m-d')
		;

		$report->setTitle('%2$s');

		$locale->getMockController()->resetCalls();

		$this->assert
			->object($report->runnerStart(new atoum\runner()))->isIdenticalTo($report)
			->mock($locale)->call('_', array('Y-m-d'))
			->mock($locale)->call('_', array('H:i:s'))
			->string($report->getTitle())->isEqualTo('H:i:s')
		;

		$report->setTitle('%1$s %2$s');

		$locale->getMockController()->resetCalls();

		$this->assert
			->object($report->runnerStart(new atoum\runner()))->isIdenticalTo($report)
			->mock($locale)->call('_', array('Y-m-d'))
			->mock($locale)->call('_', array('H:i:s'))
			->string($report->getTitle())->isEqualTo('Y-m-d H:i:s')
		;
	}
}

?>
