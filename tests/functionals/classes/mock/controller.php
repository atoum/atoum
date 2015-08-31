<?php

namespace mageekguy\atoum\tests\functionals\mock;

use mageekguy\atoum;

require_once __DIR__ . '/../../runner.php';

class timeTravel
{
	public function cloneAndModifyThisDate(\dateTime $date)
	{
		$this->startParallelTimeline($date)->modify('+1 day');
	}

	public function modifyThisDateWithoutCloning(\dateTime $date)
	{
		$date->modify('+1 day');
	}

	protected function startParallelTimeline(\dateTime $date)
	{
		return clone $date;
	}
}

class controller extends atoum\tests\functionals\test\functional
{
	/** @tags issue issue-229 mock */
	public function testCloneMock()
	{
		$this
			->given(
				$date = new \mock\DateTime(),
				$foo = new timeTravel()
			)
			->if($foo->cloneAndModifyThisDate($date))
			->then
				->mock($date)->call('modify')->never()
			->if($foo->modifyThisDateWithoutCloning($date))
			->then
				->mock($date)->call('modify')->once()
			->if($foo->cloneAndModifyThisDate($date))
			->then
				->mock($date)->call('modify')->once()
		;
	}

	/** @tags issue issue-229 mock */
	public function testClonedMockShouldBeEqual()
	{
		$this
			->given(
				$date = new \mock\DateTime(),
				$otherDate = clone $date
			)
			->then
				->object($date)->isEqualTo($otherDate)
			->if($this->calling($date)->format = 'foo')
			->then
				->object($date)->isEqualTo($otherDate)
				->string($date->format('d'))->isEqualTo('foo')
				->string($otherDate->format('d'))->isEqualTo(date('d'))
		;
	}
}
