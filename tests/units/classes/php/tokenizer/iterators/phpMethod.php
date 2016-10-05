<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use mageekguy\atoum;
use mageekguy\atoum\php\tokenizer;
use mageekguy\atoum\php\tokenizer\iterators;

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
