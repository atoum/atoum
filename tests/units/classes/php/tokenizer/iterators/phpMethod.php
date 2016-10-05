<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use mageekguy\atoum;

require_once __DIR__ . '/../../../../runner.php';

class phpMethod extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubClassOf('mageekguy\atoum\php\tokenizer\iterators\phpFunction')
        ;
    }
}
