<?php

namespace atoum\atoum\tests\units\reports\asynchronous;

use atoum\atoum;
use atoum\atoum\reports\asynchronous;

require_once __DIR__ . '/../../../runner.php';

class vim extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\reports\asynchronous::class);
    }

    public function test__construct()
    {
        $this
            ->if($report = new asynchronous\vim())
            ->then
                ->object($report->getLocale())->isEqualTo(new atoum\locale())
                ->object($report->getAdapter())->isEqualTo(new atoum\adapter())
        ;
    }
}
