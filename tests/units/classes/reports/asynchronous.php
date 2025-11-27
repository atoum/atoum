<?php

namespace atoum\atoum\tests\units\reports;

require __DIR__ . '/../../runner.php';

use atoum\atoum;

class asynchronous extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\report::class);
    }

    public function testHandleEvent()
    {
        $this
            ->if($report = new \mock\atoum\atoum\reports\asynchronous())
            ->and($report->setAdapter($adapter = new atoum\test\adapter()))
            ->when(function () use ($report) { $report->handleEvent(atoum\runner::runStop, new atoum\runner()); })
            ->then
                ->variable($report->getTitle())->isNull()
            ->if($report->setTitle($title = uniqid()))
            ->when(function () use ($report) { $report->handleEvent(atoum\runner::runStop, new atoum\runner()); })
            ->then
                ->string($report->getTitle())->isEqualTo($title)
            ->if($adapter->date = function ($format) {
                return $format;
            })
            ->and($report->setTitle('%1$s' . ($title = uniqid())))
            ->then
                ->when(function () use ($report) { $report->handleEvent(atoum\runner::runStop, new atoum\runner()); })
                ->string($report->getTitle())->isEqualTo('Y-m-d' . $title)
            ->if($report->setTitle('%1$s' . '%2$s' . ($title = uniqid())))
            ->then
                ->when(function () use ($report) { $report->handleEvent(atoum\runner::runStop, new atoum\runner()); })
                ->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . $title)
            ->if($report->setTitle('%1$s' . '%2$s' . '%3$s' . ($title = uniqid())))
            ->then
                ->when(function () use ($report) { $report->handleEvent(atoum\runner::runStop, new atoum\runner()); })
                ->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . 'SUCCESS' . $title)
            ->if($report->setTitle('%1$s' . '%2$s' . '%3$s' . ($title = uniqid())))
            ->then
                ->when(function () use ($report) { $report->handleEvent(atoum\test::success, $this); })
                ->when(function () use ($report) { $report->handleEvent(atoum\runner::runStop, new atoum\runner()); })
                ->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . 'SUCCESS' . $title)
            ->if($report->setTitle('%1$s' . '%2$s' . '%3$s' . ($title = uniqid())))
            ->then
                ->when(function () use ($report) { $report->handleEvent(atoum\test::fail, $this); })
                ->when(function () use ($report) { $report->handleEvent(atoum\runner::runStop, new atoum\runner()); })
                ->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . 'FAIL' . $title)
            ->if($report->setTitle('%1$s' . '%2$s' . '%3$s' . ($title = uniqid())))
            ->then
                ->when(function () use ($report) { $report->handleEvent(atoum\test::error, $this); })
                ->when(function () use ($report) { $report->handleEvent(atoum\runner::runStop, new atoum\runner()); })
                ->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . 'FAIL' . $title)
            ->if($report->setTitle('%1$s' . '%2$s' . '%3$s' . ($title = uniqid())))
            ->then
                ->when(function () use ($report) { $report->handleEvent(atoum\test::exception, $this); })
                ->when(function () use ($report) { $report->handleEvent(atoum\runner::runStop, new atoum\runner()); })
                ->string($report->getTitle())->isEqualTo('Y-m-d' . 'H:i:s' . 'FAIL' . $title)
        ;
    }
}
