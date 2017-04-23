<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use mageekguy\atoum;
use mageekguy\atoum\php\tokenizer;
use mageekguy\atoum\php\tokenizer\iterators;

require_once __DIR__ . '/../../../../runner.php';

class phpNamespace extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubClassOf(atoum\php\tokenizer\iterator::class)
        ;
    }

    public function testAppendClass()
    {
        $this
            ->if(
                $this->newTestedInstance,
                $phpClass = new iterators\phpClass(),
                $phpClass
                    ->append($token1 = new tokenizer\token(uniqid()))
                    ->append($token2 = new tokenizer\token(uniqid()))
            )
            ->object($this->testedInstance->appendClass($phpClass))->isTestedInstance
            ->array($this->testedInstance->getClasses())->isEqualTo([$phpClass])
            ->castToString($this->testedInstance)->isEqualTo($token1 . $token2)
        ;
    }
}
