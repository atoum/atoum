<?php

namespace mageekguy\atoum\tests\units\exceptions\runtime;

use mageekguy\atoum;

require_once __DIR__ . '/../../../runner.php';

class unexpectedValue extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(\runtimeException::class)
                ->extends(\unexpectedValueException::class)
                ->implements(atoum\exception::class)
        ;
    }
}
