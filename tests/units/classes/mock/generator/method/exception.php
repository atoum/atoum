<?php

namespace atoum\atoum\tests\units\mock\generator\method;

require __DIR__ . '/../../../../runner.php';

use atoum\atoum;

class exception extends atoum\test
{
    public function testClass()
    {
        $this->testedClass
            ->extends(\exception::class)
            ->implements(atoum\exception::class)
        ;
    }
}
