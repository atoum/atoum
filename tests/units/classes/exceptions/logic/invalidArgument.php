<?php

namespace mageekguy\atoum\tests\units\exceptions\logic;

use mageekguy\atoum;

require_once __DIR__ . '/../../../runner.php';

class invalidArgument extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(\logicException::class)
                ->extends(\invalidArgumentException::class)
                ->implements(atoum\exception::class)
        ;
    }
}
