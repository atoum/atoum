<?php

namespace mageekguy\atoum\tests\units\exceptions;

use mageekguy\atoum;

require_once __DIR__ . '/../../runner.php';

class logic extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(\logicException::class)
                ->implements(atoum\exception::class)
        ;
    }
}
