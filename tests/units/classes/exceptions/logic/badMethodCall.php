<?php

namespace atoum\atoum\tests\units\exceptions\logic;

use atoum\atoum;

require_once __DIR__ . '/../../../runner.php';

class badMethodCall extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(\logicException::class)
                ->extends(\badMethodCallException::class)
                ->implements(atoum\exception::class)
        ;
    }
}
