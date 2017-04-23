<?php

namespace mageekguy\atoum\tests\units\php\mocker;

require_once __DIR__ . '/../../../runner.php';

use mageekguy\atoum;
use mageekguy\atoum\php\mocker;
use mageekguy\atoum\php\mocker\adapter as testedClass;

class adapter extends atoum
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\test\adapter::class);
    }

    public function test__get()
    {
        $this
            ->if($adapter = new testedClass())
            ->then
                ->object($invoker = $adapter->md5)->isEqualTo(new mocker\adapter\invoker('md5'))
                ->object($adapter->md5)->isIdenticalTo($invoker)
        ;
    }
}
