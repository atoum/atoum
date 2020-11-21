<?php

namespace atoum\atoum\tests\units\asserters\adapter\call\manager;

require __DIR__ . '/../../../../../runner.php';

use atoum\atoum;

class exception extends atoum\test
{
    public function testClass()
    {
        $this->testedClass
            ->extends(\runtimeException::class)
            ->implements(atoum\exception::class)
        ;
    }
}
