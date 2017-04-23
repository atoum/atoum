<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests;

use mageekguy\atoum;
use mageekguy\atoum\runner;
use mock\mageekguy\atoum\report\fields\runner\tests\blank as testedClass;

require __DIR__ . '/../../../../../runner.php';

class blank extends atoum\test
{
    public function testClass()
    {
        $this->testedClass
            ->extends(atoum\report\field::class)
            ->isAbstract()
        ;
    }

    public function test__construct()
    {
        $this
            ->if($field = new testedClass())
            ->then
                ->variable($field->getRunner())->isNull()
                ->array($field->getEvents())->isEqualTo([runner::runStop])
        ;
    }

    public function testHandleEvent()
    {
        $this
            ->if($field = new testedClass())
            ->and($runner = new atoum\runner())
            ->then
                ->boolean($field->handleEvent(runner::runStart, $runner))->isFalse()
                ->variable($field->getRunner())->isNull()
                ->boolean($field->handleEvent(runner::runStop, $runner))->isTrue()
                ->object($field->getRunner())->isIdenticalTo($runner)
        ;
    }
}
