<?php

namespace mageekguy\atoum\tests\units\asserters\mock\exceptions;

require __DIR__ . '/../../../../runner.php';

use mageekguy\atoum;

class logic extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\exceptions\logic::class);
    }
}
