<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use mageekguy\atoum;
use mageekguy\atoum\php\tokenizer\iterators;

require_once __DIR__ . '/../../../../runner.php';

class phpArgument extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(atoum\php\tokenizer\iterator::class)
        ;
    }

    public function test__construct()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getDefaultValue())->isNull()
        ;
    }

    public function testAppendDefaultValue()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->appendDefaultValue($defaultValue = new iterators\phpDefaultValue()))->isTestedInstance
                ->object($this->testedInstance->getDefaultValue())->isIdenticalTo($defaultValue)
        ;
    }

    public function testGetDefaultValue()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getDefaultValue())->isNull()
        ;
    }
}
