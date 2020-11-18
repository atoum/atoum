<?php

namespace atoum\atoum\tests\units\mock\stream;

use atoum\atoum;
use atoum\atoum\mock\stream;
use atoum\atoum\test;

require_once __DIR__ . '/../../../runner.php';

class invoker extends test
{
    public function testClass()
    {
        $this->testedClass->isSubclassOf(atoum\test\adapter\invoker::class);
    }

    public function test__construct()
    {
        $this
            ->if($invoker = new stream\invoker($methodName = uniqid()))
            ->then
                ->string($invoker->getMethodName())->isEqualTo($methodName)
        ;
    }
}
