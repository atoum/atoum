<?php

namespace atoum\atoum\tests\units\asserters\adapter\exceptions;

require __DIR__ . '/../../../../runner.php';

use atoum\atoum;

class logic extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\exceptions\logic::class);
    }
}
