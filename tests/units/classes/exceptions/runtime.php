<?php

namespace mageekguy\atoum\tests\units\exceptions;

use mageekguy\atoum;

require_once __DIR__ . '/../../runner.php';

class runtime extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(\runtimeException::class)
                ->implements(atoum\exception::class)
        ;
    }
}
