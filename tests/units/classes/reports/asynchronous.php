<?php

namespace mageekguy\atoum\tests\units\reports;

require __DIR__ . '/../../runner.php';

use
	mageekguy\atoum,
	mock\mageekguy\atoum\reports\asynchronous as testedClass
;

class asynchronous extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->isAbstract()
			->extends('mageekguy\atoum\report')
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($report)
				->object($report->getAdapter())->isIdenticalTo($adapter)
				->object($report->setAdapter())->isIdenticalTo($report)
				->object($defaultAdapter = $report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($defaultAdapter)->isNotIdenticalTo($adapter)
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($report = new \mock\mageekguy\atoum\reports\asynchronous())
			->and($report->setAdapter($adapter = new atoum\test\adapter()))
			->then
				->object($report->handleEvent(atoum\runner::runStop, new atoum\runner()))->isIdenticalTo($report)
				->variable($report->getTitle())->isNull()
			->if($report->setTitle($title = uniqid()))
			->then
				->object($report->handleEvent(atoum\runner::runStop, new atoum\runner()))->isIdenticalTo($report)
				->string($report->getTitle())->isEqualTo($title)
			->if($adapter->date = function($format) { return $format; })
			->and($report->setTitle('%1$s' . ($title = uniqid())))
			->then
				->object($report->handleEvent(atoum\runner::runStop, new atoum\runner()))->isIdenticalTo($report)
				->string($report->getTitle())->isEqualTo('Y-m-d' . $title)
			->if($report->setTitle('%1$s' . '%2$s' . ($title = uniqid())))
			->then
				->object($report->handleEvent(atoum\runner::runStop, new atoum\runner()))->isIdenticalTo($report)
				->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . $title)
			->if($report->setTitle('%1$s' . '%2$s' . '%3$s' . ($title = uniqid())))
			->then
				->object($report->handleEvent(atoum\runner::runStop, new atoum\runner()))->isIdenticalTo($report)
				->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . 'SUCCESS' . $title)
			->if($report->setTitle('%1$s' . '%2$s' . '%3$s' . ($title = uniqid())))
			->then
				->object($report->handleEvent(atoum\test::success, $this))->isIdenticalTo($report)
				->object($report->handleEvent(atoum\runner::runStop, new atoum\runner()))->isIdenticalTo($report)
				->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . 'SUCCESS' . $title)
			->if($report->setTitle('%1$s' . '%2$s' . '%3$s' . ($title = uniqid())))
			->then
				->object($report->handleEvent(atoum\test::fail, $this))->isIdenticalTo($report)
				->object($report->handleEvent(atoum\runner::runStop, new atoum\runner()))->isIdenticalTo($report)
				->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . 'FAIL' . $title)
			->if($report->setTitle('%1$s' . '%2$s' . '%3$s' . ($title = uniqid())))
			->then
				->object($report->handleEvent(atoum\test::error, $this))->isIdenticalTo($report)
				->object($report->handleEvent(atoum\runner::runStop, new atoum\runner()))->isIdenticalTo($report)
				->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . 'FAIL' . $title)
			->if($report->setTitle('%1$s' . '%2$s' . '%3$s' . ($title = uniqid())))
			->then
				->object($report->handleEvent(atoum\test::exception, $this))->isIdenticalTo($report)
				->object($report->handleEvent(atoum\runner::runStop, new atoum\runner()))->isIdenticalTo($report)
				->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . 'FAIL' . $title)
		;
	}
}
