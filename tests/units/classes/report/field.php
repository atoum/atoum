<?php

namespace atoum\atoum\tests\units\report;

use atoum\atoum;

require_once __DIR__ . '/../../runner.php';

class field extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->isAbstract();
    }

    public function test__construct()
    {
        $this
            ->if($field = new \mock\atoum\atoum\report\field())
            ->then
                ->variable($field->getEvents())->isNull()
                ->object($field->getLocale())->isEqualTo(new atoum\locale())
        ;
    }
}
